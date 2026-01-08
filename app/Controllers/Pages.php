<?php
class Pages extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        $data = [
            'title' => 'BulkBuilder',
            'description' => 'Simple social network for sharing recipes.'
        ];

        $this->view('pages/index', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'About Us',
            'description' => 'App to share recipes with other users'
        ];

        $this->view('pages/about', $data);
    }
}
