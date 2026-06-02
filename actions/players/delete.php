<?php

require_auth();
csrf_verify();

Player::delete((int) ($_POST['id'] ?? 0));
flash('success', 'Player deleted.');
redirect('players');
