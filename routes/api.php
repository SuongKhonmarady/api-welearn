<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ExamDateController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\WorkflowScheduleController;
use App\Http\Controllers\WorkflowRunController;
use App\Http\Controllers\GitHubWebhookController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OpenAIController;

// Token-based Authentication Routes (No CSRF required)
Route::post('/auth/token-login', [AuthenticatedSessionController::class, 'tokenLogin']);
Route::middleware('auth:sanctum')->post('/auth/token-logout', [AuthenticatedSessionController::class, 'tokenLogout']);

// Current User Route
Route::middleware(['auth:sanctum'])->get('/currentUser', function (Request $request) {
    return new UserResource(Auth::user());
});

// User Routes
Route::middleware('auth:sanctum')->group(function () { 
    Route::apiResources([
        'user' => UserController::class,
    ]);
    Route::post('rank', [RankController::class, 'store']);
    Route::get('question/{category_id}/{level_id}', [QuestionController::class, 'show']);
});

Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
    Route::get('subject/{type_id}/{exam_date_id}', [SubjectController::class, 'show']);
    Route::get('quesionList/{category_id}/{level_id}/{isGraduate}', [QuestionController::class, 'listQuestionAdmin']);

    Route::post('question', [QuestionController::class, 'store']);
    Route::put('question/{question}', [QuestionController::class, 'update']);
    Route::delete('question/{question}', [QuestionController::class, 'destroy']);

    Route::post('scholarship', [ScholarshipController::class, 'store']);
    Route::put('scholarship/{scholarship}', [ScholarshipController::class, 'updateScholarship']);
    Route::delete('scholarship/{scholarship}', [ScholarshipController::class, 'destroy']);

    Route::post('type', [TypeController::class, 'store']);
    Route::put('type/{type}', [TypeController::class, 'update']);
    Route::delete('type/{type}', [TypeController::class, 'destroy']);

    Route::post('level', [LevelController::class, 'store']);
    Route::put('level/{level}', [LevelController::class, 'update']);
    Route::delete('level/{level}', [LevelController::class, 'destroy']);

    Route::post('subject', [SubjectController::class, 'store']);
    Route::put('subject/{subject}', [SubjectController::class, 'update']);
    Route::delete('subject/{subject}', [SubjectController::class, 'destroy']);

    Route::post('examDate', [ExamDateController::class, 'store']);
    Route::put('examDate/{examDate}', [ExamDateController::class, 'update']);
    Route::delete('examDate/{examDate}', [ExamDateController::class, 'destroy']);

    Route::post('category', [CategoryController::class, 'store']);
    Route::put('category/{category}', [CategoryController::class, 'update']);
    Route::delete('category/{category}', [CategoryController::class, 'destroy']);

    Route::post('countries', [CountryController::class, 'store']);
    Route::put('countries/{id}', [CountryController::class, 'update']);
    Route::delete('countries/{id}', [CountryController::class, 'destroy']);

    // Workflow Management Routes (Admin Only)
    Route::apiResource('workflows', WorkflowController::class);
    Route::post('workflows/sync', [WorkflowController::class, 'sync']);
    Route::post('workflows/{workflow}/trigger', [WorkflowController::class, 'trigger']);
    Route::get('workflows/{workflow}/runs', [WorkflowController::class, 'runs']);
    
    // Workflow Scheduling Routes
    Route::post('workflows/{workflow}/schedule', [WorkflowScheduleController::class, 'store']);
    Route::get('schedules', [WorkflowScheduleController::class, 'index']);
    Route::get('schedules/due', [WorkflowScheduleController::class, 'due']);
    Route::get('schedules/{schedule}', [WorkflowScheduleController::class, 'show']);
    Route::put('schedules/{schedule}', [WorkflowScheduleController::class, 'update']);
    Route::delete('schedules/{schedule}', [WorkflowScheduleController::class, 'destroy']);
    
    // Workflow Run Management Routes
    Route::get('runs', [WorkflowRunController::class, 'index']);
    Route::get('runs/{run}', [WorkflowRunController::class, 'show']);
    Route::get('runs/{run}/logs', [WorkflowRunController::class, 'logs']);
    Route::post('runs/{run}/cancel', [WorkflowRunController::class, 'cancel']);
    Route::post('runs/{run}/sync', [WorkflowRunController::class, 'sync']);
    Route::get('analytics', [WorkflowRunController::class, 'analytics']);
});

// GitHub Webhook (No authentication required)
// Route::post('/github/webhook', [GitHubWebhookController::class, 'handle']);

// Public Routes
Route::post('/chat-scholarship', [OpenAIController::class, 'chatRecommend']);
Route::get('level', [LevelController::class, 'index']);
Route::get('category', [CategoryController::class, 'index']);
Route::get('examDate', [ExamDateController::class, 'index']);
Route::get('type/{id}', [TypeController::class, 'show']);
Route::get('type', [TypeController::class, 'index']);
Route::get('scholarship', [ScholarshipController::class, 'index']);
Route::get('scholarship/upcoming', [ScholarshipController::class, 'upcomingScholarships']);
Route::get('scholarship/region/{region}', [ScholarshipController::class, 'filterByRegion']);
Route::get('scholarship/degree/{degree}', [ScholarshipController::class, 'filterByDegree']);
Route::get('scholarship/country/{country}', [ScholarshipController::class, 'filterByCountry']);
Route::get('scholarship/search', [ScholarshipController::class, 'search']);
Route::get('scholarship/{id}', [ScholarshipController::class, 'show']);
Route::get('pdf/{examdate_id}/{category_id}', [SubjectController::class, 'showPdf']);
Route::get('rank/{category_id}/{isGraduate}', [RankController::class, 'show']);
Route::get('countries', [CountryController::class, 'index']);
Route::get('countries/{id}', [CountryController::class, 'scholarships']);
//test route
Route::get('/status', function () {
    return response()->json(['status' => 'Laravel is working!']);
});

// Test route for CSRF and session debugging
Route::get('/test-csrf', function (Request $request) {
    return response()->json([
        'csrf_token' => $request->session()->token(),
        'session_id' => $request->session()->getId(),
        'user_authenticated' => Auth::check(),
        'user' => Auth::user(),
        'message' => 'CSRF test endpoint'
    ]);
})->middleware('web');

// Debug route to check authentication state
Route::get('/debug-auth', function (Request $request) {
    return response()->json([
        'authenticated' => Auth::check(),
        'user' => Auth::user(),
        'session_id' => session()->getId(),
        'guards' => [
            'web' => Auth::guard('web')->check(),
            'sanctum' => Auth::guard('sanctum')->check(),
        ],
        'session_data' => session()->all(),
    ]);
});