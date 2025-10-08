# Security Policy

DailyForever is a zero-knowledge platform. We rely on responsible security research to help us identify weaknesses without compromising the privacy guarantees that define the service. This document explains how to work with us and what you can expect in return.

## Supported Versions

- **[Stable Releases]** Latest tagged release on GitHub.
- **[Main Branch]** Actively maintained and deployed continuously.
- **[Other Versions]** Legacy tags and forks are out of scope; fixes are not guaranteed.

## Reporting a Vulnerability

- **[Email]** dailyforever@proton.me
- **[GitHub Security Advisory]** Use the “Report a vulnerability” option in the Security tab if you prefer GitHub’s private channel.
- **[What to Include]**
  - Reproduction steps or proof-of-concept (screenshots, logs, payloads as needed)
  - Potential impact and affected components (URLs, endpoints, user flows)
  - Mitigation ideas, if you have them
- **[Confidentiality]** Please do not open public issues or pull requests for security concerns. We appreciate keeping sensitive details private until a fix ships.

## Response Commitments

1. **Acknowledgement** within 72 hours.
2. **Triage & validation** in collaboration with you.
3. **Status updates** every 7 days until we resolve or mitigate the report.
4. **Disclosure coordination** prior to public communication. We will invite you to verify the fix before closure.

## Scope & Priorities

- **[In Scope]**
  - Exposure of plaintext data or bypass of end-to-end encryption guarantees
  - Leakage of encryption keys, IVs, or key-wrapping metadata intended to stay client-side
  - Authentication, authorization, or session handling flaws (including SRP-6a flow and private asset access rules)
  - Abuse that meaningfully impacts availability of core services (DoS, resource exhaustion)
  - Weaknesses in password-gated or addressed (KEM) shares that undermine confidentiality

- **[Out of Scope]**
  - Attacks requiring compromised end-user devices, malicious browser extensions, or physical access
  - UI issues such as clickjacking on non-production domains without security impact
  - Rate-limiting or spam bypass findings without evidence of security impact
  - Public metadata or artifacts that are intentionally exposed
  - Vulnerabilities in third-party dependencies without a demonstrated exploit path affecting DailyForever

If you are unsure whether something is in scope, reach out before testing.

## Testing Guidelines

- **[Respect Privacy]** Do not access, modify, or delete data you do not own. Use test accounts whenever possible.
- **[Limit Disruption]** Avoid automated scans against production that could degrade service for others.
- **[Fragment Secrets]** When sharing examples, redact URL fragments containing encryption keys and avoid uploading sensitive real-world data.

## Zero-Knowledge Architecture Notes

- **[Client-Side Encryption]** Content is encrypted with AES-GCM in the browser before it touches our infrastructure.
- **[Owner Convenience]** Authenticated owners have encryption keys stored by default for their own access; the setting can be disabled per account.
- **[URL Fragments]** Decryption keys live in the `#fragment` portion of share links and never leave the client.
- **[Password Gates]** Optional Argon2id gates restrict access to ciphertext but do not replace the encryption key itself.

Understanding these principles helps target research toward high-impact findings.

## Safe Harbor

We will not pursue legal action or contacts regarding reports that:

- **[Good Faith]** Follow this policy and avoid harming users or the platform.
- **[Controlled Testing]** Stop immediately if unintended access to data occurs and tell us what happened.
- **[Coordination]** Allow a reasonable remediation window before any public disclosure.

If questions arise, contact us before escalating your testing.

## Recognition

We do not currently operate a cash bounty program. With your consent, we are happy to acknowledge validated reports in release notes or security advisories.

Thank you for helping us protect DailyForever and its community.
