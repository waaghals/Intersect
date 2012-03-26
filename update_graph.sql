INSERT tblname (oridgid,destid, weight) SELECT 


SELECT a.tag_id, c.tag_id
FROM
image_tag_map AS a
JOIN image_tag_map AS b
	ON a.image_id = b.image_id
JOIN image_tag_map AS c
	ON b.image_id = b.image_id
	
	
SELECT a.tag_id as origid, b.tag_id as destid, count(1) as count, count(1) / (SELECT COUNT(*) FROM
image_tag_map AS a
JOIN image_tag_map AS b
	ON a.image_id = b.image_id
WHERE a.tag_id != b.tag_id) as weight
FROM
image_tag_map AS a
JOIN image_tag_map AS b
	ON a.image_id = b.image_id
WHERE a.tag_id != b.tag_id
GROUP BY a.tag_id, b.tag_id


## Final version, Get the similarity between tags by counting how much images the have in common.
## http://www.artfulsoftware.com/infotree/queries.php#1149  Pairwise matchmaking
## This query will insert the tag relation ships in the computation engine periodically

INSERT INTO tag_graph (origid,destid, weight)
SELECT	a.tag_id AS origid,
		b.tag_id AS destid,
		COUNT(DISTINCT a.image_id) /
	(SELECT COUNT(DISTINCT id)
	FROM image AS i
	JOIN image_tag_map AS tm 
		ON i.id = tm.image_id) 
		AS weight
FROM image_tag_map a
JOIN image_tag_map b 
	ON a.tag_id != b.tag_id
		AND a.image_id = b.image_id
GROUP BY a.tag_id,
		b.tag_id
ORDER BY weight DESC


## 99% percentile, get the top 1% of images.
SELECT 
g2.id AS id,
SUM( g1.r ) / ( 
SELECT COUNT( * ) 
FROM image ) AS percentile, CONCAT( replace( CONVERT( g2.datetime, date ) , '-', '/' ) , '/', g2.digest, '.', g2.extension ) AS path
FROM (

SELECT COUNT( * ) r, rating
FROM image
GROUP BY rating
)g1
JOIN (

SELECT COUNT( * ) r, rating, datetime, digest, extension, id
FROM image
GROUP BY rating
)g2 ON g1.rating < g2.rating
GROUP BY g2.rating
HAVING percentile >= 0.99