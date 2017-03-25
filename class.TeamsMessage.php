<?php
namespace SimpleThings;

class TeamsMessage {
    private $color;
    private $summary, $title, $text;
    private $potentialAction;
    private $sections;

    public function __construct($text = null, $title = null)
    {
        if ($text !== null) {
            $this->setTitle($text);
        }
        if ($title !== null) {
            $this->SetTitle($title);
        }
    }

    public function getJson()
    {
        $msg = array();
        if (isset($this->summary)) {
            $msg['summary'] = $this->summary;
        }
        if (isset($this->title)) {
            $msg['title'] = $this->title;
        }
        if (isset($this->text)) {
            $msg['text'] = $this->text;
        }
        if (isset($this->color)) {
            $msg['themeColor'] = $this->color;
        }
        
        if (isset($this->sections)) {
            $msg['sections'] = $this->sections;
        }
        if (isset($this->potentialAction)) {
            $msg['potentialAction'] = $this->potentialAction;
        }
        
        return json_encode($msg);
    }
    
    public function setText($text)
    {
        $this->text = $text;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
   
    
    public function setColor($color)
    {
        $this->color = $color;
    }
    
    public function addActivity($text, $title = null, $image = null)
    {
        $activity['activityTitle'] = $title;
        if ($text !== null) {
            $activity['activityText'] = $text; 
        }
        if ($image !== null) {
            $activity['activityImage'] = $image;
        }
        
        $this->sections[] = $activity;
    }

    public function addFacts($title, array $array)
    {
        $section['title'] = $title;
        foreach ($array as $name => $value) {
            $section['facts'][] = [
            'name' => $name,
            'value' => $value
            ];
        }

        $this->sections[] = $section;
    }
    
    public function addImage($title, $image)
    {
        $this->addImages($title, array($image));
    }
    
    public function addImages($title, array $images)
    {
        $section['title'] = $title;
        foreach ($images as $image) {
            $section['images'][] = ['image' => $image];
        }
        $this->sections[] = $section;
    }

    public function addAction($text, $url)
    {
        $this->potentialAction[] = [
            '@context' => 'http://schema.org',
            '@type' => 'ViewAction',
            'name' => $text,
            'target' => [$url]
        ];
    }

    public function send($url) {
        $json = $this->getJson();
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json))
        );

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        if ($result === false || $info['http_code'] != 200) {
            print_r(curl_getinfo($ch));
        }
    }
}