# Technical Documentation

Qualix is a [Laravel 11 (PHP >= 8.2)](../AGENTS.md) web application used by Swiss Pfadi/Scouting J+S course leaders to track participant observations, feedbacks, and qualifications. This page is the main navigation hub for all technical documentation — use the sections below to get onboarded, understand architectural choices, and find answers to technical questions.

***

## Getting Started

- [**Local Setup**](../README.md#lokale-installation) — Docker-based local installation, running commands in the container, and running the test suites (README, in German)
- [**AGENTS.md**](../AGENTS.md) — AI agent guidance and developer onboarding overview, including local development setup
- [**Contributing Guidelines**](../CONTRIBUTING.md) — Git workflow, pull-request checklist, testing requirements, code style, and the bilingual changelog process

***

## Architecture

- [**Application Architecture Overview**](./Architecture/11 Application Architecture Overview.md) — Laravel + Vue hybrid architecture, course-scoped resources, and route structure
- [**Domain Model & Database Schema**](./Architecture/12 Domain Model and Database Schema.md) — Relationships between Course, Participant, Block, Observation, Feedback, Requirement, and EvaluationGrid entities
- [**Frontend Architecture**](./Architecture/13 Frontend Architecture.md) — Blade + Vue 3 hybrid approach, component registration patterns, Bootstrap-Vue-Next integration, and Vite build system
- [**Authentication & Authorization**](./Architecture/14 Authentication and Authorization.md) — NativeUser vs HitobitoUser models, OAuth integration via hitobito/MiData, and parental STI pattern
- [**Security**](./Architecture/15 Security.md) — Security headers & CSP, password hashing, login throttling, and the gated E2E testing routes

***

## Features & Domain Logic

- [**Observations & Participant Tracking**](./Features/21 Observations and Participant Tracking.md) — Core observation workflow and participant progress tracking
- [**Evaluation Grids**](./Features/22 Evaluation Grids.md) — Structured assessment rubrics for participant evaluation
- [**Requirements & Qualifications**](./Features/23 Requirements and Qualifications.md) — Course requirement definitions and qualification tracking
- [**Feedback System & Collaborative Editing**](./Features/24 Feedback System and Collaborative Editing.md) — Tiptap editor integration, Yjs/y-webrtc for real-time collaboration, and the requirements matrix progress overview
- [**Feedback Allocation Algorithm**](./Features/25 Feedback Allocation Algorithm.md) — Logic for distributing feedback responsibilities among course leaders
- [**Course Setup & Teardown**](./Features/26 Course Setup and Teardown.md) — Importing blocks/participants (eCamp v3, MiData), generating blocks and participant groups, and archiving vs. deleting a course
- [**PDF Rendering**](./Features/27 PDF Rendering.md) — Client-side PDF generation for feedbacks and evaluation grids with @react-pdf/renderer

***

## Infrastructure & Operations

- [**CI Pipeline**](./Infrastructure/31 CI Pipeline.md) — GitHub Actions workflows running PHPUnit, Vitest, and Playwright test suites
- [**Environment Configuration**](./Infrastructure/32 Environment Configuration.md) — Docker setup for local development, `.env` configuration, and production secrets management
- [**Continuous Deployment**](./Infrastructure/33 Continuous Deployment.md) — Deployment workflow and process, including nightly deployments from master to production
- [**Error Tracking**](./Infrastructure/34 Error Tracking.md) — Sentry integration for production error monitoring and debugging

***

## Internationalization

- [**Translation Workflow**](./Internationalization/41 Translation Workflow.md) — German primary language and French translations using Phrase translation management

***

## Testing

- [**Testing Strategy & Guide**](./Testing/51 Testing Strategy.md) — PHPUnit for backend tests, Vitest for frontend unit tests, Playwright for E2E tests, and E2E testing routes

***

## Vision & Decisions

- [**Guiding Principles**](./Vision/61 Guiding Principles.md) — Central considerations in the business domain which led to the development of Qualix as it is today.
- [**Architecture Decisions**](./Vision/62 Architecture Decisions.md) — Key architectural decisions: why Laravel+Vue hybrid, why Vue Options API only, why traditional shared hosting, and other strategic choices
