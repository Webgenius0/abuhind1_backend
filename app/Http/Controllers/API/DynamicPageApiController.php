<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DynamicPage;
use Illuminate\Http\JsonResponse;

class DynamicPageApiController extends Controller
{
    public function index(): JsonResponse
    {
        $pages = DynamicPage::where('status', 'active')
                            ->select('id', 'page_title', 'page_slug', 'created_at')
                            ->latest()
                            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Pages fetched successfully.',
            'data'    => $pages,
        ], 200);
    }

    public function show(string $pageSlug): JsonResponse
    {
        $page = DynamicPage::where('page_slug', $pageSlug)
                            ->where('status', 'active')
                            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found.',
            ], 404);
        }

        $responseData = $page->toArray();

        // Jodi slug 'faq' hoy, tahole HTML content parse kore dynamic FAQ array banabo
        // Jodi slug 'faq' hoy, tahole HTML content parse kore dynamic FAQ array banabo
        if ($pageSlug === 'faq' && !empty($page->page_content)) {
            $faqs = [];

            $dom = new \DOMDocument();
            @$dom->loadHTML('<?encoding utf-8?>' . $page->page_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            // ekhane loop chalanor somoy h3 er sathe h4 gulo o dhorar jonno query use korte paren
            // ba sohoj vabe h3 ebong h4 dutoi alada ba ek sathe loop chalate paren
            $xpath = new \DOMXPath($dom);
            // Sob h3 ebong h4 tag gulo query kore anchi
            $headings = $xpath->query('//h3 | //h4');

            foreach ($headings as $heading) {
                $question = trim($heading->nodeValue);
                $answer = '';

                $next = $heading->nextSibling;
                while ($next) {
                    if ($next->nodeName === 'p') {
                        $answer = trim($next->nodeValue);
                        break;
                    }
                    $next = $next->nextSibling;
                }

                if (!empty($question)) {
                    $faqs[] = [
                        'question' => $question,
                        'answer'   => $answer,
                    ];
                }
            }

            $responseData['faqs'] = $faqs;
        }

        return response()->json([
            'success' => true,
            'message' => 'Page details fetched successfully.',
            'data'    => $responseData,
        ], 200);
    }
}
