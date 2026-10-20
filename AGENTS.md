# Agent instructions

## Skills

Project skills are directories in `.agents/skills`. Each skill has a `SKILL.md` file. The YAML frontmatter has `name` and `description`. The body is the procedure.

When the user asks what skills there are, which skills are available, or what a skill does:

1. Read the directories in `.agents/skills` at that moment.
2. Read each directory's `SKILL.md`.
3. Explain the skills from those files.

Do not answer from a list written in this file, a remembered list, or any other hardcoded catalog. This file does not name the skills. If `.agents/skills` is missing or has no `SKILL.md` files, say there are no project skills.

When a task matches a description you just read, follow that `SKILL.md`.
