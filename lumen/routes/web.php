<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$slimBaseUrl = rtrim(env('SLIM_API_URL', 'http://slim:8000'), '/');
$expressBaseUrl = rtrim(env('EXPRESS_API_URL', 'http://api_express:5555'), '/');

$httpRequest = function (string $method, string $url, ?array $body, array $headers) {
    $payload = $body !== null ? json_encode($body) : null;
    $allHeaders = array_merge(
        ['Accept: application/json'],
        $payload ? ['Content-Type: application/json'] : [],
        $headers
    );

    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $allHeaders),
            'content' => $payload ?? '',
            'ignore_errors' => true,
            'timeout' => 10,
        ],
    ]);

    $responseBody = @file_get_contents($url, false, $context);
    $status = 500;
    if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
        $status = (int) $matches[1];
    }

    $decoded = json_decode($responseBody ?? '', true);
    $data = json_last_error() === JSON_ERROR_NONE ? $decoded : $responseBody;

    return [$status, $data];
};

$forwardAuth = function (Request $request): array {
    $auth = $request->header('Authorization');
    return $auth ? ["Authorization: {$auth}"] : [];
};

$router->group(['prefix' => 'auth'], function () use ($router, $slimBaseUrl, $httpRequest) {
    $router->post('/register', function (Request $request) use ($slimBaseUrl, $httpRequest) {
        [$status, $data] = $httpRequest('POST', "{$slimBaseUrl}/auth/register", $request->all(), []);
        return response()->json($data, $status);
    });

    $router->post('/login', function (Request $request) use ($slimBaseUrl, $httpRequest) {
        [$status, $data] = $httpRequest('POST', "{$slimBaseUrl}/auth/login", $request->all(), []);
        return response()->json($data, $status);
    });
});

$router->group(['prefix' => 'users'], function () use ($router, $slimBaseUrl, $httpRequest, $forwardAuth) {
    $router->get('/', function (Request $request) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('GET', "{$slimBaseUrl}/users", null, $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->get('/{id}', function (Request $request, string $id) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('GET', "{$slimBaseUrl}/users/{$id}", null, $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->post('/', function (Request $request) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('POST', "{$slimBaseUrl}/users", $request->all(), $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->put('/{id}', function (Request $request, string $id) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('PUT', "{$slimBaseUrl}/users/{$id}", $request->all(), $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->delete('/{id}', function (Request $request, string $id) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('DELETE', "{$slimBaseUrl}/users/{$id}", null, $forwardAuth($request));
        return response()->json($data, $status);
    });
});

$router->group(['prefix' => 'messages'], function () use ($router, $slimBaseUrl, $httpRequest, $forwardAuth) {
    $router->get('/', function (Request $request) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('GET', "{$slimBaseUrl}/messages", null, $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->get('/me', function (Request $request) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('GET', "{$slimBaseUrl}/messages/me", null, $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->get('/{id}', function (Request $request, string $id) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('GET', "{$slimBaseUrl}/messages/{$id}", null, $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->post('/', function (Request $request) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('POST', "{$slimBaseUrl}/messages", $request->all(), $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->put('/{id}', function (Request $request, string $id) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('PUT', "{$slimBaseUrl}/messages/{$id}", $request->all(), $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->delete('/{id}', function (Request $request, string $id) use ($slimBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('DELETE', "{$slimBaseUrl}/messages/{$id}", null, $forwardAuth($request));
        return response()->json($data, $status);
    });
});

$router->group(['prefix' => 'discussions'], function () use ($router, $expressBaseUrl, $httpRequest, $forwardAuth) {
    $router->get('/', function (Request $request) use ($expressBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('GET', "{$expressBaseUrl}/discussions", null, $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->get('/{id}', function (Request $request, string $id) use ($expressBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('GET', "{$expressBaseUrl}/discussions/{$id}", null, $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->post('/', function (Request $request) use ($expressBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('POST', "{$expressBaseUrl}/discussions", $request->all(), $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->put('/{id}', function (Request $request, string $id) use ($expressBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('PUT', "{$expressBaseUrl}/discussions/{$id}", $request->all(), $forwardAuth($request));
        return response()->json($data, $status);
    });

    $router->delete('/{id}', function (Request $request, string $id) use ($expressBaseUrl, $httpRequest, $forwardAuth) {
        [$status, $data] = $httpRequest('DELETE', "{$expressBaseUrl}/discussions/{$id}", null, $forwardAuth($request));
        return response()->json($data, $status);
    });
});

$router->get('/discussions/{id}/with-last-message', function (Request $request, string $id) use (
    $expressBaseUrl,
    $slimBaseUrl,
    $httpRequest,
    $forwardAuth
) {
    [$discussionStatus, $discussion] = $httpRequest(
        'GET',
        "{$expressBaseUrl}/discussions/{$id}",
        null,
        $forwardAuth($request)
    );

    if ($discussionStatus !== 200 || !is_array($discussion)) {
        return response()->json($discussion, $discussionStatus);
    }

    $lastMessageId = $discussion['lastMessageId'] ?? null;
    if (!$lastMessageId) {
        return response()->json([
            'discussion' => $discussion,
            'lastMessage' => null,
        ], 200);
    }

    [$messageStatus, $message] = $httpRequest(
        'GET',
        "{$slimBaseUrl}/messages/{$lastMessageId}",
        null,
        $forwardAuth($request)
    );

    if ($messageStatus !== 200) {
        return response()->json([
            'discussion' => $discussion,
            'lastMessage' => null,
            'lastMessageError' => $message,
        ], 200);
    }

    return response()->json([
        'discussion' => $discussion,
        'lastMessage' => $message,
    ], 200);
});

$router->get('/discussions/{id}/participants', function (Request $request, string $id) use (
    $expressBaseUrl,
    $slimBaseUrl,
    $httpRequest,
    $forwardAuth
) {
    [$discussionStatus, $discussion] = $httpRequest(
        'GET',
        "{$expressBaseUrl}/discussions/{$id}",
        null,
        $forwardAuth($request)
    );

    if ($discussionStatus !== 200 || !is_array($discussion)) {
        return response()->json($discussion, $discussionStatus);
    }

    $participants = $discussion['participants'] ?? [];
    if (!is_array($participants) || count($participants) === 0) {
        return response()->json([
            'discussion' => $discussion,
            'participants' => [],
        ], 200);
    }

    $users = [];
    foreach ($participants as $userId) {
        [$status, $user] = $httpRequest(
            'GET',
            "{$slimBaseUrl}/users/{$userId}",
            null,
            $forwardAuth($request)
        );
        if ($status === 200) {
            $users[] = $user;
        }
    }

    return response()->json([
        'discussion' => $discussion,
        'participants' => $users,
    ], 200);
});


$router->get('/conversations', function (Request $request) use (
    $expressBaseUrl,
    $slimBaseUrl,
    $httpRequest,
    $forwardAuth
) {
    [$discussionsStatus, $discussions] = $httpRequest(
        'GET',
        "{$expressBaseUrl}/discussions",
        null,
        $forwardAuth($request)
    );

    if ($discussionsStatus !== 200 || !is_array($discussions)) {
        return response()->json([
            'error' => 'Failed to fetch discussions',
            'details' => $discussions
        ], $discussionsStatus);
    }

    $conversations = [];
    foreach ($discussions as $discussion) {
        $conversationData = [
            'id' => $discussion['_id'] ?? $discussion['id'] ?? null,
            'title' => $discussion['title'] ?? null,
            'createdBy' => $discussion['createdBy'] ?? null,
            'createdAt' => $discussion['createdAt'] ?? null,
            'updatedAt' => $discussion['updatedAt'] ?? null,
            'participants' => [],
            'lastMessage' => null,
        ];

        $participantIds = $discussion['participants'] ?? [];
        if (is_array($participantIds)) {
            foreach ($participantIds as $userId) {
                [$userStatus, $user] = $httpRequest(
                    'GET',
                    "{$slimBaseUrl}/users/{$userId}",
                    null,
                    $forwardAuth($request)
                );
                if ($userStatus === 200 && is_array($user)) {
                    $conversationData['participants'][] = [
                        'id' => $user['id'] ?? null,
                        'username' => $user['username'] ?? null,
                        'first_name' => $user['first_name'] ?? null,
                        'last_name' => $user['last_name'] ?? null,
                    ];
                }
            }
        }

        $lastMessageId = $discussion['lastMessageId'] ?? null;
        if ($lastMessageId) {
            [$messageStatus, $messageData] = $httpRequest(
                'GET',
                "{$slimBaseUrl}/messages/{$lastMessageId}",
                null,
                $forwardAuth($request)
            );
            if ($messageStatus === 200) {
                $message = is_array($messageData) && isset($messageData['data'])
                    ? $messageData['data']
                    : $messageData;

                if (is_array($message)) {
                    $conversationData['lastMessage'] = [
                        'id' => $message['id'] ?? null,
                        'content' => $message['content'] ?? null,
                        'user_id' => $message['user_id'] ?? null,
                        'created_at' => $message['created_at'] ?? null,
                    ];
                }
            }
        }

        $conversations[] = $conversationData;
    }

    return response()->json([
        'conversations' => $conversations,
        'total' => count($conversations)
    ], 200);
});

$router->get('/conversations/{id}', function (Request $request, string $id) use (
    $expressBaseUrl,
    $slimBaseUrl,
    $httpRequest,
    $forwardAuth
) {
    [$discussionStatus, $discussion] = $httpRequest(
        'GET',
        "{$expressBaseUrl}/discussions/{$id}",
        null,
        $forwardAuth($request)
    );

    if ($discussionStatus !== 200 || !is_array($discussion)) {
        return response()->json([
            'error' => 'Discussion not found',
            'details' => $discussion
        ], $discussionStatus);
    }

    $conversation = [
        'id' => $discussion['_id'] ?? $discussion['id'] ?? $id,
        'title' => $discussion['title'] ?? null,
        'createdBy' => $discussion['createdBy'] ?? null,
        'createdAt' => $discussion['createdAt'] ?? null,
        'updatedAt' => $discussion['updatedAt'] ?? null,
        'participants' => [],
        'messages' => [],
    ];

    $participantIds = $discussion['participants'] ?? [];
    $participantsMap = [];

    if (is_array($participantIds)) {
        foreach ($participantIds as $userId) {
            [$userStatus, $userData] = $httpRequest(
                'GET',
                "{$slimBaseUrl}/users/{$userId}",
                null,
                $forwardAuth($request)
            );
            if ($userStatus === 200) {
                $user = is_array($userData) && isset($userData['data'])
                    ? $userData['data']
                    : $userData;

                if (is_array($user)) {
                    $userInfo = [
                        'id' => $user['id'] ?? null,
                        'username' => $user['username'] ?? null,
                        'first_name' => $user['first_name'] ?? null,
                        'last_name' => $user['last_name'] ?? null,
                    ];
                    $conversation['participants'][] = $userInfo;
                    $participantsMap[$userId] = $userInfo;
                }
            }
        }
    }

    [$messagesStatus, $allMessagesData] = $httpRequest(
        'GET',
        "{$slimBaseUrl}/messages",
        null,
        $forwardAuth($request)
    );

    if ($messagesStatus === 200) {
        $allMessages = is_array($allMessagesData) && isset($allMessagesData['data'])
            ? $allMessagesData['data']
            : $allMessagesData;

            if (is_array($allMessages)) {
            foreach ($allMessages as $message) {
                if (!is_array($message)) continue;

                $msgDiscussionId = $message['discussionId'] ?? $message['discussion_id'] ?? null;

                if ($msgDiscussionId === $id) {
                    $messageUserId = $message['userId'] ?? $message['user_id'] ?? null;

                    $messageData = [
                        'id' => $message['id'] ?? null,
                        'content' => $message['content'] ?? null,
                        'user_id' => $messageUserId,
                        'discussion_id' => $msgDiscussionId,
                        'created_at' => $message['createdAt'] ?? $message['created_at'] ?? null,
                    ];

                    if ($messageUserId && isset($participantsMap[$messageUserId])) {
                        $messageData['user'] = $participantsMap[$messageUserId];
                    }

                    $conversation['messages'][] = $messageData;
                }
            }

            if (!empty($conversation['messages'])) {
                usort($conversation['messages'], function ($a, $b) {
                    $dateA = $a['created_at'] ?? '';
                    $dateB = $b['created_at'] ?? '';
                    return strcmp($dateA, $dateB);
                });
            }
        }
    }

    return response()->json($conversation, 200);
});

$router->post('/conversations', function (Request $request) use (
    $expressBaseUrl,
    $slimBaseUrl,
    $httpRequest,
    $forwardAuth
) {
    $data = $request->all();

    if (!isset($data['participants']) || !is_array($data['participants'])) {
        return response()->json(['error' => 'participants is required and must be an array'], 400);
    }

    if (count($data['participants']) < 2) {
        return response()->json(['error' => 'At least 2 participants are required'], 400);
    }

    if (!isset($data['createdBy']) || !is_int($data['createdBy']) || $data['createdBy'] <= 0) {
        return response()->json(['error' => 'createdBy is required and must be a positive integer'], 400);
    }

    $validParticipants = [];
    foreach ($data['participants'] as $userId) {
        if (!is_int($userId) || $userId <= 0) {
            continue;
        }

        [$userStatus, $user] = $httpRequest(
            'GET',
            "{$slimBaseUrl}/users/{$userId}",
            null,
            $forwardAuth($request)
        );
        if ($userStatus === 200) {
            $validParticipants[] = $userId;
        }
    }

    if (count($validParticipants) < 2) {
        return response()->json(['error' => 'At least 2 valid participants required'], 400);
    }

    [$createdByStatus, $createdByUser] = $httpRequest(
        'GET',
        "{$slimBaseUrl}/users/{$data['createdBy']}",
        null,
        $forwardAuth($request)
    );

    if ($createdByStatus !== 200) {
        return response()->json(['error' => 'createdBy user not found'], 400);
    }

    $payload = [
        'participants' => $validParticipants,
        'createdBy' => $data['createdBy'],
    ];

    if (isset($data['title']) && is_string($data['title'])) {
        $payload['title'] = $data['title'];
    }

    [$discussionStatus, $discussion] = $httpRequest(
        'POST',
        "{$expressBaseUrl}/discussions",
        $payload,
        $forwardAuth($request)
    );

    if ($discussionStatus !== 201 && $discussionStatus !== 200) {
        return response()->json([
            'error' => 'Failed to create discussion',
            'details' => $discussion
        ], $discussionStatus);
    }

    return response()->json([
        'message' => 'Conversation created successfully',
        'conversation' => $discussion
    ], 201);
});

$router->post('/conversations/{id}/messages', function (Request $request, string $id) use (
    $expressBaseUrl,
    $slimBaseUrl,
    $httpRequest,
    $forwardAuth
) {
    [$discussionStatus, $discussion] = $httpRequest(
        'GET',
        "{$expressBaseUrl}/discussions/{$id}",
        null,
        $forwardAuth($request)
    );

    if ($discussionStatus !== 200 || !is_array($discussion)) {
        return response()->json(['error' => 'Discussion not found'], 404);
    }

    $data = $request->all();

    if (!isset($data['content']) || empty($data['content'])) {
        return response()->json(['error' => 'content is required'], 400);
    }

    if (!isset($data['user_id']) || !is_int($data['user_id']) || $data['user_id'] <= 0) {
        return response()->json(['error' => 'user_id is required and must be a positive integer'], 400);
    }

    $participants = $discussion['participants'] ?? [];
    if (!in_array($data['user_id'], $participants)) {
        return response()->json(['error' => 'User is not a participant of this conversation'], 403);
    }

    $messageData = [
        'discussion_id' => $id,
        'content' => $data['content'],
        'user_id' => $data['user_id'],
    ];

    [$messageStatus, $messageResponse] = $httpRequest(
        'POST',
        "{$slimBaseUrl}/messages",
        $messageData,
        $forwardAuth($request)
    );

    if ($messageStatus !== 201 && $messageStatus !== 200) {
        return response()->json([
            'error' => 'Failed to create message',
            'details' => $messageResponse
        ], $messageStatus);
    }

    $message = is_array($messageResponse) && isset($messageResponse['data'])
        ? $messageResponse['data']
        : $messageResponse;

    $messageId = is_array($message) ? ($message['id'] ?? null) : null;

    if ($messageId) {
        $httpRequest(
            'PUT',
            "{$expressBaseUrl}/discussions/{$id}",
            ['lastMessageId' => $messageId],
            $forwardAuth($request)
        );
    }

    return response()->json([
        'message' => 'Message added successfully',
        'data' => $message
    ], 201);
});

$router->put('/conversations/{id}', function (Request $request, string $id) use (
    $expressBaseUrl,
    $httpRequest,
    $forwardAuth
) {
    $data = $request->all();

    if (empty($data)) {
        return response()->json(['error' => 'At least one field is required'], 400);
    }

    [$status, $result] = $httpRequest(
        'PUT',
        "{$expressBaseUrl}/discussions/{$id}",
        $data,
        $forwardAuth($request)
    );

    return response()->json($result, $status);
});

$router->delete('/conversations/{id}', function (Request $request, string $id) use (
    $expressBaseUrl,
    $httpRequest,
    $forwardAuth
) {
    [$discussionStatus, $result] = $httpRequest(
        'DELETE',
        "{$expressBaseUrl}/discussions/{$id}",
        null,
        $forwardAuth($request)
    );

    if ($discussionStatus !== 200 && $discussionStatus !== 204) {
        return response()->json([
            'error' => 'Failed to delete discussion',
            'details' => $result
        ], $discussionStatus);
    }

    return response()->json([
        'message' => 'Conversation deleted successfully'
    ], 200);
});
