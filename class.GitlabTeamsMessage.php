<?php
namespace SimpleThings;
require_once "class.TeamsMessage.php";

class GitlabTeamsMessage extends TeamsMessage {
    private $gitlab_json;
    private $debug;
    private $input;

    public function __construct($json, $debug = false) {
        $this->gitlab_json = $json;
        $this->debug = $debug;
        
        $this->input = json_decode($json,true);
        if(!isset($this->input) && !isset($this->input['object_kind'])) {
            throw new ErrorException("Can't parse gitlab json input");
        }
        
        switch($this->input['object_kind']) {
            case 'note':
                $this->parseNote();
                break;
            case 'issue':
                $this->parseIssue();
                break;
            case 'merge_request':
                $this->parseMergeRequest();
                break;
            case 'push':
                $this->parsePush();
                break;
            case 'tag_push':
                $this->parseTagPush();
                break;
            case 'pipeline':
                $this->parsePipeline();
                break;
            case 'build':
                $this->parseBuild();
                break;
            case 'wiki_page':
                $this->parseWikiPage();
                break;
            default:
                $this->parseUnknownInput();
                break;
        }
    }
    
    function parseUnknownInput() {
        $this->setTitle("Unknown Message: ".$this->input['project']['name']);
        $this->setText("Kind: ".$this->input['object_kind']);
        $this->setColor("FF0000");
    }

    function parseNote() {
        $this->setText("Note to ".$this->input['object_attributes']['noteable_type']." in ".$this->input['project']['name']);
        $this->addActivity($this->input['object_attributes']['note'],
            $this->input['user']['name'],
            $this->input['user']['avatar_url']);
        
        $this->addAction('View',$this->input['object_attributes']['url']);
    }

    function parseIssue() {
        $this->setText("New Issue in ".$this->input['project']['name']);
        $this->addActivity($this->input['object_attributes']['note'],
            $this->input['user']['name'].": ".$this->input['object_attributes']['title'],
            $this->input['user']['avatar_url']);
        
        $this->addAction('View',$this->input['object_attributes']['url']);
    }

    function parseMergeRequest() {
        $this->setText("MR ".$this->input['object_attributes']['source_branch']." -> ".$this->input['object_attributes']['target_branch']);
        $this->addActivity($this->input['object_attributes']['description'],
            $this->input['user']['name'].": ".$this->input['object_attributes']['title'],
            $this->input['user']['avatar_url']);
        
        $this->addAction('View',$this->input['object_attributes']['url']);
    }

    function parsePush() {
        $this->setText("Push in ".$this->input['ref']);
        $this->addActivity("has ".$this->input['total_commits_count']." Commits in ".$this->input['ref']." pushed.",
            $this->input['user_name'],
            $this->input['user_avatar']);

        $commits = array();
        for($i = 0; $i < $this->input['total_commits_count']; $i++) {
            $commit = $this->input['commits'][$i];
            $commits[($i + 1)."."] = "[".$commit['message']."](".$commit['url'].")";
        }

        $this->addFacts("", $commits);
    }

    function parseTagPush() {
        $this->setText("New Tag ".$this->input['ref']);
        $this->addActivity("",
            $this->input['user_name'],
            $this->input['user_avatar']);
    }

    function parsePipeline() {
        $this->setText("Pipeline ".$this->input['object_attributes']['ref']." (".$this->input['object_attributes']['status'].")");
        $this->addActivity($this->input['commit']['message'],
            $this->input['user']['name'].": ",
            $this->input['user']['avatar_url']);

        $this->addAction('View Commit',$this->input['commit']['url']);
    }

    function parseBuild() {
        $this->setText("Build ".$this->input['ref']);
        $this->addActivity($this->input['build_status'],
            $this->input['user']['name'].": ".$this->input['build_name'],
            $this->input['user']['avatar_url']);

        $this->addAction('View Repository',$this->input['repository']['git_http_url']);
    }

    function parseWikiPage() {
        $this->setText("WikiPage ".$this->input['object_attributes']['title']." (".$this->input['object_attributes']['action'].")");
        $this->addActivity($this->input['object_attributes']['message'],
            $this->input['user']['name'].": ",
            $this->input['user']['avatar_url']);

        $this->addAction('View WikiPage',$this->input['object_attributes']['url']);
    }
}