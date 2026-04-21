<?php

namespace App\Controllers;

class AiAssistant extends BaseController
{
    public function index()
    {
        // Load the AI assistant view
        return view('ai_assistant'); 
    }
}
