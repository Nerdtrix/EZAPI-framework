<?php
    namespace Tests;
    use PHPUnit\Framework\TestCase;
    use Exception;
    use Models\User;
    use Services\AuthService;

    use function PHPUnit\Framework\assertEquals;
    use function PHPUnit\Framework\assertTrue;

    class AuthServiceTest extends TestCase
    {
        private FakeUserAuthRepository $authRepository;
        private AuthService $service;

        public function setUp() : void
        {
            $this->authRepository = new FakeUserAuthRepository();
            $this->service = new AuthService(
                $this->authRepository
            );
        }

        public function testAuthenticate() : void
        {
            // Auth repository.
            $getUserCount = 0;
            $this->authRepository->getUserByEmailCallback = function($email) use (&$getUserCount)
            {
                assertEquals("john@mail.com", $email);
                $getUserCount += 1;
                $user = new User();
                $user->username = "test";
                return $user;
            };

            // Test.
            $result = $this->service->authenticate("john@mail.com", "12345", true);

            // Assert.
            assertEquals(1, $getUserCount);
            assertEquals("test", $result->username);
        }

        public function testAuthenticateWithUsername() : void
        {
            // Auth repository.
            $getUserEmailCount = 0;
            $this->authRepository->getUserByEmailCallback = function($email) use (&$getUserEmailCount)
            {
                assertEquals("john", $email);
                $getUserEmailCount += 1;
                return null;
            };
            $getUserUsernameCount = 0;
            $this->authRepository->getUserByUsernameCallback = function($username) use (&$getUserUsernameCount)
            {
                assertEquals("john", $username);
                $getUserUsernameCount += 1;
                $user = new User();
                $user->username = "test";
                return $user;
            };

            // Test.
            $result = $this->service->authenticate("john", "12345", true);

            // Assert.
            assertEquals(1, $getUserEmailCount);
            assertEquals(1, $getUserUsernameCount);
            assertEquals("test", $result->username);
        }

        public function testAuthenticateUserNotFound()
        {
            // Auth repository.
            $getUserEmailCount = 0;
            $this->authRepository->getUserByEmailCallback = function($email) use (&$getUserEmailCount)
            {
                assertEquals("john", $email);
                $getUserEmailCount += 1;
                return null;
            };
            $getUserUsernameCount = 0;
            $this->authRepository->getUserByUsernameCallback = function($username) use (&$getUserUsernameCount)
            {
                assertEquals("john", $username);
                $getUserUsernameCount += 1;
                return null;
            };

            // Test.
            $error = false;

            try
            {
                $this->service->authenticate("john", "12345", true);
            }
            catch (Exception $ex)
            {
                assertEquals("user_not_found", $ex->getMessage());
                $error = true;
            }

            // Assert.
            assertTrue($error);
            assertEquals(1, $getUserEmailCount);
            assertEquals(1, $getUserUsernameCount);
        }
    }
?>
