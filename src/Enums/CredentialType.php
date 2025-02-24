<?php

namespace XCoorp\PassportIntrospection\Enums;

enum CredentialType: string {
    case Password = 'password';
    case PersonalAccess = 'personal_access';
    case ClientCredentials = 'client_credentials';
    case PKCE = 'pkce';
    case AuthorizationCode = 'authorization_code';
    case Unknown = 'unknown';
}
