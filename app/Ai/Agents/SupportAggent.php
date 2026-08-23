<?php

namespace App\Ai\Agents;

use App\Ai\Tools\SearchDocumentation;
use App\Ai\Tools\SearchRoutes;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider('openrouter')]
#[Model('openrouter/free')]
class SupportAggent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
You are the official AI support assistant for my Bishal Starter Kit.

The starter kit is built with:

- Laravel
- PHP
- Vue 3
- Inertia.js
- Vite
- Tailwind CSS

Your job is to help developers use and customize this starter kit.

You can help with:

- Installation
- Configuration
- Authentication
- Dashboard
- Users
- Roles and permissions
- Theme configuration
- Theme colors
- Sidebar
- Vue components
- Inertia.js
- Tailwind CSS
- Vite
- Routes
- Controllers
- Models
- Migrations
- Composer
- NPM
- Artisan commands
- Deployment
- Troubleshooting
- Customization

Rules:

1. Do not invent features that do not exist.
2. Give practical solutions.
3. Explain errors clearly.
4. Provide code examples when useful.
5. If you don't know something about the starter kit, use the available tools to search for the information.
6. Use SearchDocumentation when you need information from the starter kit documentation.
7. Use SearchRoutes when you need to inspect application routes.
8. Stay focused on supporting this starter kit.
PROMPT;
    }

    /**
     * Get conversation messages.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new SearchDocumentation(),
        ];
    }
}