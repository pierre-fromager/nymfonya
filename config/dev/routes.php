<?php

return [
    '/!\.(ico|xml|txt|avi|htm|zip|js|ico|gif|jpg|JPG|png|css|swf|flv|m4v|mp3|mp4|ogv|webm|woff)$/',
    '/^(api\/v1\/transavia)$/', // 1st group match controller with default action
    '/^(api\/v1\/transavia)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
    '/^(api\/v1\/transavia)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
    '/^(api\/v1\/transavia)\/(.*)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/lufthansa)$/', // 1st group match controller with default action
    '/^(api\/v1\/lufthansa)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
    '/^(api\/v1\/lufthansa)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
    '/^(api\/v1\/lufthansa)\/(.*)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/iberia)$/', // 1st group match controller with default action
    '/^(api\/v1\/iberia)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
    '/^(api\/v1\/iberia)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
    '/^(api\/v1\/iberia)\/(.*)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/britishairways)$/', // 1st group match controller with default action
    '/^(api\/v1\/britishairways)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
    '/^(api\/v1\/britishairways)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
    '/^(api\/v1\/britishairways)\/(.*)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/common)$/', // 1st group match controller with default action
    '/^(api\/v1\/common)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
    '/^(api\/v1\/common)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
    '/^(api\/v1\/common)\/(.*)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/auth)$/', // 1st group match controller with default action
    '/^(api\/v1\/auth)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
    '/^(api\/v1\/auth)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
    '/^(api\/v1\/auth)\/(.*)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/stat)\/(opcache)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/stat)\/(filecache)$/', // 1st group match controller 2nd match action
];
