<?php

return [
    '/!\.(ico|xml|txt|avi|htm|zip|js|ico|gif|jpg|JPG|png|css|swf|flv|m4v|mp3|mp4|ogv|webm|woff)$/',
    '/^(api\/v1\/stat)\/(opcache)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/stat)\/(filecache)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/test)\/(pokerelay)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/auth)$/', // 1st group match controller with default action
    '/^(api\/v1\/auth)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
    '/^(api\/v1\/auth)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
    '/^(api\/v1\/auth)\/(.*)$/', // 1st group match controller 2nd match action
    '/^(config)\/(help)$/', // 1st group match controller 2nd match action
    '/^(config)\/(keygen)$/', // 1st group match controller 2nd match action
    '/^(config)\/(account)$/', // 1st group match controller 2nd match action
    '/^(config)\/(false)$/', // 1st group match controller 2nd match action
    '/^(config)\/(swaggerdoc)$/', // 1st group match controller 2nd match action
    '/^(api\/v1\/restful)$/', // 1st group match controller with default action
    '/^(api\/v1\/restful)(\?.*)/', // 3rd group match ?a=1&b=2
    '/^(api\/v1\/restful)\/(.*?)(\?.*)/', // 3rd group match ?a=1&b=2
    '/^(api\/v1\/restful)\/(.*?)(\/.*)/', // 3rd group match /a/1/b/2
    '/^(api\/v1\/restful)\/(.*)$/', // 1st group match controller 2nd match action
];
