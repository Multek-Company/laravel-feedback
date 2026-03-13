<?php

namespace Multek\LaravelFeedback\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Multek\LaravelFeedback\FeedbackManager;
use Multek\LaravelFeedback\Http\Requests\StoreFeedbackRequest;

class FeedbackController extends Controller
{
    public function __construct(
        protected FeedbackManager $manager,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = $this->manager->query()->latest();

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        return response()->json($query->paginate());
    }

    public function store(StoreFeedbackRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()?->getKey();

        $feedback = $this->manager->create($data);

        return response()->json($feedback, 201);
    }
}
