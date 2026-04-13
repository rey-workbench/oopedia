# Adaptive Rules Domain Guide

**Generated:** 2026-04-11 (Asia/Bangkok)
**Parent:** `/AGENTS.md`

## OVERVIEW

Forward-chaining adaptive engine: facts (`Gxx`) are evaluated by prioritized rules to emit actions (`Hxx`) that drive remediation, promotion, certification, and intervention.

## STRUCTURE

```text
app/Rules/Adaptive/
├── BaseAdaptiveRule.php            # Shared predicates/helpers for rule evaluation
├── Contracts/AdaptiveRuleInterface.php
├── Constants/AdaptiveConstants.php # Canonical FACT/ACTION codes
├── Concerns/                       # Reusable apply/evaluate fragments
├── RuleRegistry.php                # Ordered registration by priority
└── Rule*.php                       # Concrete policy rules
```

## WHERE TO LOOK

| Task                         | File                                  |
| ---------------------------- | ------------------------------------- |
| Add or reorder rules         | `RuleRegistry.php`                    |
| Add new fact/action code     | `Constants/AdaptiveConstants.php`     |
| Shared predicate/apply logic | `BaseAdaptiveRule.php`, `Concerns/*`  |
| Rule contract expectations   | `Contracts/AdaptiveRuleInterface.php` |

## CONVENTIONS

- All rule identifiers, names, action codes, and priorities are declared on each concrete rule.
- Priority ordering is ascending numeric in `RuleRegistry` (`lower = higher precedence`).
- Facts/actions must use `AdaptiveConstants`; no magic literals in services/pages.
- Rule `evaluate()` is side-effect free; state mutation happens only in `apply()`.

## CHANGE PROTOCOL

1. Add/modify constants in `AdaptiveConstants` if new semantic is needed.
2. Implement rule class extending `BaseAdaptiveRule`.
3. Register class in `RuleRegistry` and validate priority relative to existing rules.
4. Verify behavior through adaptive service tests (`tests/Feature/Unit/Services/Adaptive/*`).

## ANTI-PATTERNS

- Encoding hard-coded G/H codes directly in controllers/services/frontend.
- Changing `RuleRegistry` order without considering precedence side effects.
- Putting business side effects in `evaluate()`.
