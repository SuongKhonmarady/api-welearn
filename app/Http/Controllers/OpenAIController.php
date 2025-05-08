<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Scholarship;

class OpenAIController extends Controller
{
    public function chatRecommend(Request $request)
    {
        $userMessage = $request->input('message');

        if (!$userMessage) {
            return response()->json(['error' => 'Message required'], 400);
        }

        // Get latest scholarships with more details
        $scholarships = Scholarship::orderBy('post_at', 'desc')
            ->limit(10)
            ->get([
                'title',
                'description',
                'official_link',
                'deadline',
                'eligibility',
                'host_country',
                'host_university',
                'program_duration',
                'degree_offered'
            ])
            ->toArray();

        // Format scholarships with more comprehensive information
        $formatted = "";
        foreach ($scholarships as $index => $s) {
            $formatted .= ($index + 1) . ". ";
            $formatted .= isset($s['title']) ? "🎓 " . $s['title'] . "\n" : "";
            $formatted .= "📝 Description: " . $s['description'] . "\n";
            $formatted .= isset($s['official_link']) ? "🔗 Apply here: " . $s['official_link'] . "\n" : "";
            $formatted .= isset($s['host_country']) ? "🌍 Host Country: " . $s['host_country'] . "\n" : "";
            $formatted .= isset($s['host_university']) ? "🏛️ University: " . $s['host_university'] . "\n" : "";
            $formatted .= isset($s['program_duration']) ? "⏱️ Duration: " . $s['program_duration'] . "\n" : "";
            $formatted .= isset($s['degree_offered']) ? "📜 Degree: " . $s['degree_offered'] . "\n" : "";
            $formatted .= isset($s['eligibility']) ? "✅ Eligibility: " . $s['eligibility'] . "\n" : "";
            $formatted .= isset($s['deadline']) ? "⏰ Deadline: " . $s['deadline'] . "\n" : "";
            $formatted .= "\n";
        }

        // Build prompt for OpenAI with more context
        $fullPrompt = <<<PROMPT
User asked: "{$userMessage}"

Here are the latest scholarship opportunities with detailed information:

{$formatted}

Please provide a friendly and helpful response to guide the user through available scholarships. Format your response as follows:

👋 Greeting:
- Start with a warm greeting
- Acknowledge their interest in scholarships

🎯 Suggested Matches:
- "Based on your interest, here are some great options for you!"
- Highlight 3-5 best matching scholarships and why they're particularly suitable
- Mention if there are more similar opportunities available

💫 Top Recommendations:
[For each recommended scholarship]
• 🎓 Program: [Title & University]
• ✨ Why this fits you: [2-3 key points why this matches their interests]
• 📋 Key Requirements: [2-3 main eligibility points]
• ⏰ Important Dates: [Application deadline & key dates]
• 🔗 Next Steps: [How to apply with link]

💡 Additional Options:
- Briefly mention other available opportunities (if any)
- Suggest related programs they might also consider

🤝 Closing Note:
- Encourage them to explore these opportunities
- Offer to help narrow down choices or answer specific questions
- Remind them you can help find more options if these don't match their interests

Keep your tone friendly and enthusiastic while being informative. Help them feel confident about their options.
PROMPT;

        // Call OpenAI API
        $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'You are an enthusiastic and knowledgeable scholarship advisor. Focus on matching scholarships to user interests, highlighting why each option is great for them. Be encouraging and helpful, like an experienced mentor who wants to see them succeed. Make them feel confident about the many opportunities available.'
                ],
                ['role' => 'user', 'content' => $fullPrompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 600 // Increased slightly to accommodate more detailed recommendations
        ]);

        $rawReply = $response->json()['choices'][0]['message']['content'] ?? 'Sorry, no response from OpenAI.';

        // Optional: Convert to HTML-friendly format (for frontend chat display)
        $formattedReply = nl2br(e($rawReply)); // This changes \n to <br> and escapes HTML

        return response()->json([
            'reply' => $rawReply
        ]);
        
    }
}
