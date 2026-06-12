# Logo Soup WP Plugin — CrewAI Planning Crew

This directory is a [CrewAI](https://docs.crewai.com/) project. It runs a **planner** agent that turns a short task description into an implementation plan (markdown) suitable for `.cursor/plans/` in the repo root.

## Prerequisites

- **Python 3.10–3.13**
- **uv** (recommended): `curl -LsSf https://astral.sh/uv/install.sh | sh`
- **LLM API key** in `crewai/.env` (copy from `.env.example`)

## Setup

```bash
cd crewai
uv tool install crewai   # if needed
crewai install
cp .env.example .env     # set OPENAI_API_KEY
```

## Run

```bash
cd crewai
crewai run
# or
uv run run_crew "Add admin screen for logo upload"
```

Output: `crewai/plan_output.md` → copy to `.cursor/plans/PLAN.md` for the two-phase plan → implement workflow.

## Layout

- `src/logo_soup_wp_plugin/` — crew package (`main.py`, `crew.py`, `config/*.yaml`)
