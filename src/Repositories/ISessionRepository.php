<?php
    namespace Repositories;

    interface ISessionRepository
    {
        function listByUserId(int $userId) : \stdClass;

        function getBySessionToken(string $token): \Models\SessionModel;

        function getByUserId(int $userId): \Models\SessionModel;

        function deleteById(int $sessionId): bool;

        function extendExpirationTime(int $time, string $sessionToken) : bool;

        function getDateTimeFormat() : string;

        function deleteByToken(string $token): bool;

        function create(int $userId, string $token, int $expiresAt) : bool;
    }
?>