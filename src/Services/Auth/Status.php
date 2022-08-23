<?php
    namespace Services\Auth;

    enum Status : string
    {
      const ACTIVE = "ACTIVE";
      const BANNED = "BANNED";
      const INACTIVE = "INACTIVE";
      const BLOCKED = "BLOCKED";
    }
?>