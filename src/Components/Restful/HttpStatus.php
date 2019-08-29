<?php

namespace InnStudio\Prober\Components\Restful;

class HttpStatus
{
    public static $__default = 200;

    public static $CONTINUE = 100;

    public static $SWITCHING_PROTOCOLS = 101;

    public static $PROCESSING = 102; // WEBDAV;_RFC_2518

    public static $OK = 200;

    public static $CREATED = 201;

    public static $ACCEPTED = 202;

    public static $NON_AUTHORITATIVE_INFORMATION = 203; // SINCE_HTTP/1.1

    public static $NO_CONTENT = 204;

    public static $RESET_CONTENT = 205;

    public static $PARTIAL_CONTENT = 206;

    public static $MULTI_STATUS = 207; // WEBDAV;_RFC_4918

    public static $ALREADY_REPORTED = 208; // WEBDAV;_RFC_5842

    public static $IM_USED = 226; // RFC_3229

    public static $MULTIPLE_CHOICES = 300;

    public static $MOVED_PERMANENTLY = 301;

    public static $FOUND = 302;

    public static $SEE_OTHER = 303; // SINCE_HTTP/1.1

    public static $NOT_MODIFIED = 304;

    public static $USE_PROXY = 305; // SINCE_HTTP/1.1

    public static $SWITCH_PROXY = 306;

    public static $TEMPORARY_REDIRECT = 307; // SINCE_HTTP/1.1

    public static $PERMANENT_REDIRECT = 308; // APPROVED_AS_EXPERIMENTAL_RFC

    public static $BAD_REQUEST = 400;

    public static $UNAUTHORIZED = 401;

    public static $PAYMENT_REQUIRED = 402;

    public static $FORBIDDEN = 403;

    public static $NOT_FOUND = 404;

    public static $METHOD_NOT_ALLOWED = 405;

    public static $NOT_ACCEPTABLE = 406;

    public static $PROXY_AUTHENTICATION_REQUIRED = 407;

    public static $REQUEST_TIMEOUT = 408;

    public static $CONFLICT = 409;

    public static $GONE = 410;

    public static $LENGTH_REQUIRED = 411;

    public static $PRECONDITION_FAILED = 412;

    public static $REQUEST_ENTITY_TOO_LARGE = 413;

    public static $REQUEST_URI_TOO_LONG = 414;

    public static $UNSUPPORTED_MEDIA_TYPE = 415;

    public static $REQUESTED_RANGE_NOT_SATISFIABLE = 416;

    public static $EXPECTATION_FAILED = 417;

    public static $I_AM_A_TEAPOT = 418;

    public static $AUTHENTICATION_TIMEOUT = 419; // NOT_IN_RFC_2616

    public static $ENHANCE_YOUR_CALM = 420; // TWITTER

    public static $METHOD_FAILURE = 420; // SPRING_FRAMEWORK

    public static $UNPROCESSABLE_ENTITY = 422; // WEBDAV;_RFC_4918

    public static $LOCKED = 423; // WEBDAV;_RFC_4918

    public static $FAILED_DEPENDENCY = 424; // WEBDAV

    public static $UNORDERED_COLLECTION = 425; // INTERNET_DRAFT

    public static $UPGRADE_REQUIRED = 426; // RFC_2817

    public static $PRECONDITION_REQUIRED = 428; // RFC_6585

    public static $TOO_MANY_REQUESTS = 429; // RFC_6585

    public static $REQUEST_HEADER_FIELDS_TOO_LARGE = 431; // RFC_6585

    public static $NO_RESPONSE = 444; // NGINX

    public static $RETRY_WITH = 449; // MICROSOFT

    public static $BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450; // MICROSOFT

    public static $REDIRECT = 451; // MICROSOFT

    public static $UNAVAILABLE_FOR_LEGAL_REASONS = 451; // INTERNET_DRAFT

    public static $REQUEST_HEADER_TOO_LARGE = 494; // NGINX

    public static $CERT_ERROR = 495; // NGINX

    public static $NO_CERT = 496; // NGINX

    public static $HTTP_TO_HTTPS = 497; // NGINX

    public static $CLIENT_CLOSED_REQUEST = 499; // NGINX

    public static $INTERNAL_SERVER_ERROR = 500;

    public static $NOT_IMPLEMENTED = 501;

    public static $BAD_GATEWAY = 502;

    public static $SERVICE_UNAVAILABLE = 503;

    public static $GATEWAY_TIMEOUT = 504;

    public static $HTTP_VERSION_NOT_SUPPORTED = 505;

    public static $VARIANT_ALSO_NEGOTIATES = 506; // RFC_2295

    public static $INSUFFICIENT_STORAGE = 507; // WEBDAV;_RFC_4918

    public static $LOOP_DETECTED = 508; // WEBDAV;_RFC_5842

    public static $BANDWIDTH_LIMIT_EXCEEDED = 509; // APACHE_BW/LIMITED_EXTENSION

    public static $NOT_EXTENDED = 510; // RFC_2774

    public static $NETWORK_AUTHENTICATION_REQUIRED = 511; // RFC_6585

    public static $NETWORK_READ_TIMEOUT_ERROR = 598; // UNKNOWN

    public static $NETWORK_CONNECT_TIMEOUT_ERROR = 599; // Unknown
}
