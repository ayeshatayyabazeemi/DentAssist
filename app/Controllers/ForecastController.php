<?php

namespace App\Controllers;

class ForecastController extends BaseController
{
    public function index()
    {
        $jsonFile = FCPATH . 'python_script/forecast_finance.json';
        $maxAge = 86400; // 24 hours

        echo "STEP 1: Controller hit<br>";

        // ==========================
        // CHECK IF PYTHON SHOULD RUN
        // ==========================
        if (!file_exists($jsonFile) || (time() - filemtime($jsonFile)) > $maxAge) {

            echo "STEP 2: File missing or outdated → running pipeline<br>";

            $this->runPythonPipeline();
        } else {
            echo "STEP 2: File is fresh → skipping Python run<br>";
        }

        // ==========================
        // READ JSON
        // ==========================
        if (!file_exists($jsonFile)) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "JSON file not found"
            ]);
        }

        $data = file_get_contents($jsonFile);

        echo "STEP 3: Returning JSON<br>";

        return $this->response->setJSON(json_decode($data, true));
    }

    // ==========================
    // PYTHON PIPELINE
    // ==========================
  private function runPythonPipeline()
{
    echo "STEP 4: Running Python pipeline<br>";

    $basePath = FCPATH . 'python_script' . DIRECTORY_SEPARATOR;
    $python = "C:\\Program Files\\Python310\\python.exe";

    $scripts = [
        "fetch_data.py",
        "preprocess.py",
        "forecast.py"
    ];

    foreach ($scripts as $script) {

        $scriptPath = $basePath . $script;

        if (!file_exists($scriptPath)) {
            echo "SKIP: $script<br>";
            continue;
        }

        // 🔥 FIRE AND FORGET (NO WAIT)
        $cmd = "start /B \"\" \"$python\" \"$scriptPath\" > NUL 2>&1";

        pclose(popen($cmd, "r"));

        echo "TRIGGERED: $script<br>";
    }

    echo "STEP 5: Pipeline triggered (async)<br>";
}
}