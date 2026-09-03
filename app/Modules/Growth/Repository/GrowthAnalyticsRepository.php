<?php

declare(strict_types=1);

namespace App\Modules\Growth\Repository;

use App\Core\BaseRepository;
use PDO;

final class GrowthAnalyticsRepository extends BaseRepository
{
    protected string $table = 'growth_page_views';
    protected bool $softDeletes = false;

    public function recordPageView(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO growth_page_views (visitor_hash,path,page_type,entity_id,title,referrer_host,utm_source,utm_medium,utm_campaign) VALUES (:visitor_hash,:path,:page_type,:entity_id,:title,:referrer_host,:utm_source,:utm_medium,:utm_campaign)');
        $stmt->execute([
            'visitor_hash'=>$data['visitor_hash'],'path'=>$data['path'],'page_type'=>$data['page_type']??null,'entity_id'=>$data['entity_id']??null,
            'title'=>$data['title']??null,'referrer_host'=>$data['referrer_host']??null,'utm_source'=>$data['utm_source']??null,
            'utm_medium'=>$data['utm_medium']??null,'utm_campaign'=>$data['utm_campaign']??null,
        ]);
    }

    public function recordEvent(array $data): void
    {
        $stmt=$this->db->prepare('INSERT INTO growth_events (visitor_hash,event_name,path,service_id,entity_id,metadata) VALUES (:visitor_hash,:event_name,:path,:service_id,:entity_id,:metadata)');
        $stmt->execute([
            'visitor_hash'=>$data['visitor_hash'],'event_name'=>$data['event_name'],'path'=>$data['path']??null,'service_id'=>$data['service_id']??null,
            'entity_id'=>$data['entity_id']??null,'metadata'=>!empty($data['metadata'])?json_encode($data['metadata'],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR):null,
        ]);
    }

    public function summary(int $days): array
    {
        $days=max(1,min(365,$days));
        $sql='SELECT
            (SELECT COUNT(*) FROM growth_page_views WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL '.$days.' DAY)) page_views,
            (SELECT COUNT(DISTINCT visitor_hash) FROM growth_page_views WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL '.$days.' DAY)) visitors,
            (SELECT COUNT(*) FROM growth_events WHERE event_name="assistance_request_created" AND occurred_at >= DATE_SUB(NOW(), INTERVAL '.$days.' DAY)) assistance_requests,
            (SELECT COUNT(*) FROM growth_events WHERE event_name="assistant_handoff" AND occurred_at >= DATE_SUB(NOW(), INTERVAL '.$days.' DAY)) assistant_handoffs,
            (SELECT COUNT(*) FROM growth_events WHERE event_name="quote_accepted" AND occurred_at >= DATE_SUB(NOW(), INTERVAL '.$days.' DAY)) quotes_accepted,
            (SELECT COUNT(*) FROM growth_events WHERE event_name="payment_verified" AND occurred_at >= DATE_SUB(NOW(), INTERVAL '.$days.' DAY)) payments_verified,
            (SELECT COUNT(*) FROM growth_events WHERE event_name="assistance_completed" AND occurred_at >= DATE_SUB(NOW(), INTERVAL '.$days.' DAY)) completed';
        $row=$this->db->query($sql)->fetch()?:[]; foreach($row as $k=>$v)$row[$k]=(int)$v; return $row;
    }

    public function topPages(int $days,int $limit=15): array
    {
        $stmt=$this->db->prepare('SELECT path,MAX(title) title,COUNT(*) views,COUNT(DISTINCT visitor_hash) visitors FROM growth_page_views WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL :days DAY) GROUP BY path ORDER BY views DESC,visitors DESC LIMIT :limit');
        $stmt->bindValue(':days',max(1,min(365,$days)),PDO::PARAM_INT);$stmt->bindValue(':limit',max(1,min(50,$limit)),PDO::PARAM_INT);$stmt->execute();return $stmt->fetchAll();
    }

    public function sources(int $days,int $limit=12): array
    {
        $stmt=$this->db->prepare('SELECT COALESCE(NULLIF(utm_source,""),NULLIF(referrer_host,""),"direct") source,COUNT(*) views,COUNT(DISTINCT visitor_hash) visitors FROM growth_page_views WHERE viewed_at >= DATE_SUB(NOW(), INTERVAL :days DAY) GROUP BY source ORDER BY views DESC LIMIT :limit');
        $stmt->bindValue(':days',max(1,min(365,$days)),PDO::PARAM_INT);$stmt->bindValue(':limit',max(1,min(30,$limit)),PDO::PARAM_INT);$stmt->execute();return $stmt->fetchAll();
    }

    public function eventCounts(int $days): array
    {
        $stmt=$this->db->prepare('SELECT event_name,COUNT(*) total FROM growth_events WHERE occurred_at >= DATE_SUB(NOW(), INTERVAL :days DAY) GROUP BY event_name ORDER BY total DESC');
        $stmt->bindValue(':days',max(1,min(365,$days)),PDO::PARAM_INT);$stmt->execute();return $stmt->fetchAll();
    }

    public function servicePerformance(int $days,int $limit=15): array
    {
        $stmt=$this->db->prepare('SELECT s.id,s.name,s.slug,COALESCE(v.views,0) views,COALESCE(v.visitors,0) visitors,COALESCE(e.requests,0) requests,COALESCE(e.quotes,0) quotes,COALESCE(e.paid,0) paid,COALESCE(e.completed,0) completed FROM services s LEFT JOIN (SELECT entity_id,COUNT(*) views,COUNT(DISTINCT visitor_hash) visitors FROM growth_page_views WHERE page_type="service" AND viewed_at >= DATE_SUB(NOW(), INTERVAL :views_days DAY) GROUP BY entity_id) v ON v.entity_id=s.id LEFT JOIN (SELECT service_id,SUM(event_name="assistance_request_created") requests,SUM(event_name="quote_created") quotes,SUM(event_name="payment_verified") paid,SUM(event_name="assistance_completed") completed FROM growth_events WHERE occurred_at >= DATE_SUB(NOW(), INTERVAL :events_days DAY) GROUP BY service_id) e ON e.service_id=s.id WHERE s.status="published" AND s.deleted_at IS NULL ORDER BY views DESC,requests DESC LIMIT :limit');
        $stmt->bindValue(':views_days',max(1,min(365,$days)),PDO::PARAM_INT);$stmt->bindValue(':events_days',max(1,min(365,$days)),PDO::PARAM_INT);$stmt->bindValue(':limit',max(1,min(50,$limit)),PDO::PARAM_INT);$stmt->execute();return $stmt->fetchAll();
    }

    public function assistantIntentInsights(int $days,int $limit=15): array
    {
        $stmt=$this->db->prepare('SELECT m.message,s.id session_id,s.completed_at,(SELECT COUNT(*) FROM assistant_service_matches sm WHERE sm.session_id=s.id) match_count,m.created_at FROM assistant_messages m JOIN assistant_sessions s ON s.id=m.session_id WHERE m.direction="user" AND m.created_at >= DATE_SUB(NOW(), INTERVAL :days DAY) ORDER BY m.created_at DESC LIMIT :limit');
        $stmt->bindValue(':days',max(1,min(365,$days)),PDO::PARAM_INT);$stmt->bindValue(':limit',max(1,min(100,$limit)),PDO::PARAM_INT);$stmt->execute();return $stmt->fetchAll();
    }

    public function contentGaps(int $days,int $limit=20): array
    {
        $sql='SELECT * FROM (
            SELECT "service" content_type,s.id entity_id,s.name title,COALESCE(v.views,0) views,
                   CASE WHEN s.meta_title IS NULL OR s.meta_title="" THEN 1 ELSE 0 END missing_title,
                   CASE WHEN s.meta_description IS NULL OR s.meta_description="" THEN 1 ELSE 0 END missing_description,
                   CASE WHEN sx.customer_fee IS NULL AND sx.pricing_mode IS NULL THEN 1 ELSE 0 END missing_commerce
            FROM services s LEFT JOIN service_commerce sx ON sx.service_id=s.id
            LEFT JOIN (SELECT entity_id,COUNT(*) views FROM growth_page_views WHERE page_type="service" AND viewed_at >= DATE_SUB(NOW(), INTERVAL '.$days.' DAY) GROUP BY entity_id) v ON v.entity_id=s.id
            WHERE s.status="published" AND s.deleted_at IS NULL
            UNION ALL
            SELECT "page" content_type,p.id entity_id,p.title,0 views,
                   CASE WHEN p.meta_title IS NULL OR p.meta_title="" THEN 1 ELSE 0 END,
                   CASE WHEN p.meta_description IS NULL OR p.meta_description="" THEN 1 ELSE 0 END,
                   CASE WHEN p.focus_keyword IS NULL OR p.focus_keyword="" THEN 1 ELSE 0 END
            FROM pages p WHERE p.status="published" AND p.deleted_at IS NULL AND p.noindex=0
            UNION ALL
            SELECT "blog" content_type,bp.id entity_id,bp.title,0 views,
                   CASE WHEN bp.meta_title IS NULL OR bp.meta_title="" THEN 1 ELSE 0 END,
                   CASE WHEN bp.meta_description IS NULL OR bp.meta_description="" THEN 1 ELSE 0 END,
                   CASE WHEN bp.excerpt IS NULL OR bp.excerpt="" THEN 1 ELSE 0 END
            FROM blog_posts bp WHERE bp.status="published" AND bp.deleted_at IS NULL
        ) gaps WHERE missing_title=1 OR missing_description=1 OR missing_commerce=1 ORDER BY (missing_title+missing_description+missing_commerce) DESC,views DESC LIMIT '.$limit;
        return $this->db->query($sql)->fetchAll();
    }

    public function openNotes(int $limit=50): array { $stmt=$this->db->prepare('SELECT * FROM growth_content_notes WHERE status="open" ORDER BY FIELD(priority,"high","medium","low"),created_at DESC LIMIT :limit');$stmt->bindValue(':limit',max(1,min(100,$limit)),PDO::PARAM_INT);$stmt->execute();return $stmt->fetchAll(); }
    public function dismissNote(int $id): void { $this->db->prepare('UPDATE growth_content_notes SET status="dismissed",updated_at=NOW() WHERE id=:id')->execute(['id'=>$id]); }
}
