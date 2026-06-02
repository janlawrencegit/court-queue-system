<?php

require_auth();

$exclude = [];
if (!empty($_GET['exclude'])) {
    foreach (explode(',', (string) $_GET['exclude']) as $id) {
        $id = (int) trim($id);
        if ($id > 0) {
            $exclude[] = $id;
        }
    }
}

json_response(Player::search($_GET['q'] ?? '', $exclude));
