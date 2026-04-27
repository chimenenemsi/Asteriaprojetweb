<?php
namespace App\Models;

class StaticPage
{
    private $id;
    private $slug;
    private $title;
    private $eyebrow;
    private $intro;
    private $summary;
    private $image;
    private $type;
    private $content_json;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->slug = htmlspecialchars(trim($data['slug'] ?? ''));
        $this->title = htmlspecialchars(trim($data['title'] ?? ''));
        $this->eyebrow = htmlspecialchars(trim($data['eyebrow'] ?? ''));
        $this->intro = htmlspecialchars(trim($data['intro'] ?? ''));
        $this->summary = htmlspecialchars(trim($data['summary'] ?? ''));
        $this->image = $data['image'] ?? null;
        $this->type = $data['type'] ?? 'FRONT';
        $this->content_json = $data['content_json'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getSlug() { return $this->slug; }
    public function getTitle() { return $this->title; }
    public function getEyebrow() { return $this->eyebrow; }
    public function getIntro() { return $this->intro; }
    public function getSummary() { return $this->summary; }
    public function getType() { return $this->type; }
}
