<?php

namespace Project\AiDirectory;

require_once __DIR__ . '/../vendor/autoload.php'; // Ensure autoloading is enabled

use GuzzleHttp\Client;
use Dotenv\Dotenv;

class Categorizer
{
    private $client;
    private $apiKey;
    private $localCategories;
    private $logDir;

    public function __construct()
    {
        // Load environment variables
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $this->apiKey = $_ENV['GEMINI_API_KEY'];
        $this->client = new Client();

        // Set logs directory
        $this->logDir = __DIR__ . '/../logs';

        // Ensure logs directory exists
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }

        // Predefined MIME type categories for local processing
        $this->localCategories = [
            'text/plain' => 'Text',
            'image/jpeg' => 'Image',
            'image/png' => 'Image',
            'image/gif' => 'Image',
            'audio/mpeg' => 'Audio',
            'audio/wav' => 'Audio',
            'video/mp4' => 'Video',
            'video/x-matroska' => 'Video',
            'application/pdf' => 'Document',
            'application/msword' => 'Document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Document',
            'application/vnd.ms-excel' => 'Spreadsheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Spreadsheet',
            'application/vnd.ms-powerpoint' => 'Presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'Presentation'
        ];
    }

    public function categorize($filePath)
    {
        if (!file_exists($filePath)) {
            return "Error: File does not exist.";
        }

        // Get file MIME type
        $fileMimeType = mime_content_type($filePath);
        $fileName = basename($filePath);

        // Step 1: Check local categories first
        if (isset($this->localCategories[$fileMimeType])) {
            $category = $this->localCategories[$fileMimeType];
            $this->logClassification($fileName, $fileMimeType, $category, "Local");
            return $category;
        }

        // Step 2: If not found locally, request AI categorization
        $prompt = "Classify the file based on its MIME type.\n";
        $prompt .= "MIME Type: $fileMimeType\n";
        $prompt .= "Choose one category: Text, Image, Video, Audio, Document, Spreadsheet, Presentation.\n";
        $prompt .= "Only return the category name without any explanation.";

        try {
            $response = $this->client->post("https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent", [
                'query' => ['key' => $this->apiKey],
                'json' => [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            // Extract AI response
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $category = trim($data['candidates'][0]['content']['parts'][0]['text']);
                $this->logClassification($fileName, $fileMimeType, $category, "AI");
                return $category;
            }

            return "Unknown"; // Fallback if AI doesn't return expected response

        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            return "Error: Unable to categorize file.";
        }
    }

    private function logClassification($fileName, $mimeType, $category, $method)
    {
        $logEntry = date('Y-m-d H:i:s') . " - [$method] $fileName ($mimeType) -> $category\n";
        file_put_contents($this->logDir . '/classification.log', $logEntry, FILE_APPEND);
    }

    private function logError($message)
    {
        $errorLog = date('Y-m-d H:i:s') . " - ERROR: " . $message . "\n";
        file_put_contents($this->logDir . '/error.log', $errorLog, FILE_APPEND);
    }
}
