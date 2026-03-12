<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AgentController as BaseAgentController;

class AgentAdminController extends BaseAgentController
{
    // Inherits all actions from the base AgentController.
    // Admin routes are non-localized and central; base controller already
    // handles central vs tenant context via TenantManager.
}
