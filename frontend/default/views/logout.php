<?php

$this->kwitang->logout();

$redirect = $this->input->get('redirect');

if ( ! empty ($redirect)) {
    redirect ($redirect);
} else {
    redirect (site_url ());
}
