# API Token Authentication Guide

This document explains how to create and use API tokens for the Tourism Booking System.

## Overview

The application uses **Laravel Sanctum** for token-based API authentication. Tokens allow secure API access without exposing user credentials.

## Token Management Endpoints

### 1. Create an API Token

**Endpoint:** `POST /api/tokens`

**Authentication:** Required (Bearer token or session cookie)

**Request Body:**
```json
{
  "token_name": "My App Token"
}
```

**Response:**
```json
{
  "message": "API token created successfully.",
  "token": "1|dxf12j3k4l5m6n7o8p9q0r1s2t3u4v5w6x7y8z",
  "token_name": "My App Token"
}
```

**cURL Example:**
```bash
curl -X POST http://localhost:8000/api/tokens \
  -H "Authorization: Bearer YOUR_EXISTING_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"token_name": "Mobile App"}'
```

---

### 2. List All API Tokens

**Endpoint:** `GET /api/tokens`

**Authentication:** Required

**Response:**
```json
{
  "message": "API tokens retrieved successfully.",
  "tokens": [
    {
      "id": 1,
      "name": "My App Token",
      "last_used_at": "2026-06-15T10:30:00.000000Z",
      "created_at": "2026-06-15T10:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Dashboard Token",
      "last_used_at": null,
      "created_at": "2026-06-15T10:15:00.000000Z"
    }
  ]
}
```

**cURL Example:**
```bash
curl -X GET http://localhost:8000/api/tokens \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 3. Revoke a Single Token

**Endpoint:** `DELETE /api/tokens/{tokenId}`

**Authentication:** Required

**Response:**
```json
{
  "message": "API token revoked successfully."
}
```

**cURL Example:**
```bash
curl -X DELETE http://localhost:8000/api/tokens/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 4. Revoke All Tokens

**Endpoint:** `POST /api/tokens/revoke-all`

**Authentication:** Required

**Response:**
```json
{
  "message": "All API tokens revoked successfully."
}
```

**cURL Example:**
```bash
curl -X POST http://localhost:8000/api/tokens/revoke-all \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Using API Tokens

Once you have created a token, include it in the `Authorization` header of all API requests:

```bash
Authorization: Bearer YOUR_API_TOKEN
```

### Example API Request with Token

```bash
curl -X GET http://localhost:8000/api/bookings \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json"
```

---

## Initial Token Generation Flow

For users who need to generate their first token via web login:

1. **User logs in** through the web interface (`POST /login` or `/admin/login`)
2. **User visits token management page** (not yet created, can be added to dashboard)
3. **User creates a token** by calling `POST /api/tokens`
4. **User stores the token securely** (it won't be shown again)
5. **User uses token** for API calls with `Authorization: Bearer <token>`

---

## Token Security Best Practices

✅ **DO:**
- Store tokens securely (environment variables, secure vaults)
- Use HTTPS for all API requests
- Rotate tokens regularly
- Create separate tokens for different applications/devices
- Revoke tokens when no longer needed

❌ **DON'T:**
- Commit tokens to version control
- Share tokens in logs or error messages
- Use the same token across multiple applications
- Store tokens in localStorage (web) or plain text

---

## Protected API Endpoints

All endpoints requiring `auth:sanctum` middleware require a valid API token:

- `GET /api/bookings`
- `GET /api/payments`
- `POST /api/bookings`
- `POST /api/packages`
- `PUT /api/bookings/{booking}`
- `PUT /api/packages/{package}`
- `DELETE /api/bookings/{booking}`
- And more (see `routes/api.php`)

---

## Error Responses

### Unauthorized (Missing/Invalid Token)
```json
{
  "message": "Unauthenticated."
}
```
HTTP Status: `401`

### Not Found (Token ID doesn't exist)
```json
{
  "message": "Token not found."
}
```
HTTP Status: `404`

### Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "token_name": ["The token name field is required."]
  }
}
```
HTTP Status: `422`

---

## Testing with Postman

1. Create a new request in Postman
2. Set method to `POST` and URL to `http://localhost:8000/api/tokens`
3. Go to **Auth** tab → Select "Bearer Token" type
4. Enter your existing token
5. Go to **Body** tab → Select "raw" and "JSON"
6. Enter:
   ```json
   {
     "token_name": "Postman Token"
   }
   ```
7. Click **Send**
8. Copy the returned token and use it in subsequent requests

---

## Additional Notes

- Tokens are automatically revoked when a user account is deleted
- Admin users can create and manage tokens just like regular users
- Tokens have no expiration by default (can be customized in Sanctum config)
- Each token creation generates a unique plain-text token shown only once
