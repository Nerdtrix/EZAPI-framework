<?php
    namespace Repositories;

    interface ISessionRepository
    {
        function listByUserId(int $userId) : object;

        function getBySessionToken(string $token): \Models\SessionModel;

        function getByUserId(int $userId, int $deviceId): \Models\SessionModel;

        function deleteById(int $sessionId): bool;

        function extendExpirationTime(int $time, string $sessionToken) : bool;

        function getDateTimeFormat() : string;

        function deleteByToken(string $token): bool;

        public function create(int $userId, int $deviceId, string $token, bool $isValidated, string $expiresAt) : bool;
    }
?>