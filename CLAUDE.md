## graphify

Knowledge graph at `graphify-out/` with god nodes, community structure, cross-file relationships.

Rules:
- Codebase questions: run `graphify query "<question>"` if `graphify-out/graph.json` exists. Use `graphify path "<A>" "<B>"` for relationships, `graphify explain "<concept>"` for concepts. Returns scoped subgraph, smaller than `GRAPH_REPORT.md` or raw grep.
- If `graphify-out/wiki/index.md` exists, use for navigation instead of raw source.
- Read `graphify-out/GRAPH_REPORT.md` only for broad architecture or if query/path/explain fail.
- After code modify, run `graphify update .` to keep graph current (AST-only, no API cost).
