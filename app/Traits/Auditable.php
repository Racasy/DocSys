<?php

   namespace App\Traits;

   use App\Models\AuditLog;
   use Illuminate\Support\Facades\Auth;

   trait Auditable
   {
       protected function logAudit(string $action, array $details = []): void
       {
           AuditLog::create([
               'user_id' => Auth::id(),
               'action' => $action,
               'details' => $details,
           ]);
       }
   }