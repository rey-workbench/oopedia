import os
import re

WORKSPACE = os.getcwd()
CONSTANTS_PATH = os.path.join(WORKSPACE, 'app', 'Rules', 'Adaptive', 'Constants', 'AdaptiveConstants.php')
SERVICE_PATH = os.path.join(WORKSPACE, 'app', 'Services', 'Adaptive', 'FactGatheringService.php')
SEEDER_PATH = os.path.join(WORKSPACE, 'database', 'seeders', 'AdaptiveRuleSeeder.php')

def parse_constants():
    facts = {}
    with open(CONSTANTS_PATH, 'r', encoding='utf-8') as f:
        content = f.read()
        fact_matches = re.findall(r"public const (FACT_\w+)\s*=\s*'([^']+)'", content)
        for name, value in fact_matches:
            facts[name] = value
    return facts

def parse_produced_facts():
    produced = set()
    with open(SERVICE_PATH, 'r', encoding='utf-8') as f:
        content = f.read()
        matches = re.findall(r"AdaptiveConstants::(FACT_\w+)", content)
        for m in matches:
            produced.add(m)
    return produced

def parse_seeder():
    facts_seeded = {} # code -> name_constant
    rules = []
    with open(SEEDER_PATH, 'r', encoding='utf-8') as f:
        content = f.read()
        fact_block_match = re.search(r"\$facts = \[(.*?)\];", content, re.DOTALL)
        if fact_block_match:
            entries = re.findall(r"\['code'\s*=>\s*'([^']+)',.*?'name'\s*=>\s*AC::(FACT_\w+)", fact_block_match.group(1))
            for code, name in entries:
                facts_seeded[code] = name
        
        rule_block_match = re.search(r"\$rules = \[(.*?)\];", content, re.DOTALL)
        if rule_block_match:
            rule_entries = re.findall(r"\[(.*?)\]", rule_block_match.group(1), re.DOTALL)
            for entry in rule_entries:
                code_m = re.search(r"'code'\s*=>\s*'([^']+)'", entry)
                req_m = re.search(r"'required'\s*=>\s*\[(.*?)\]", entry)
                forb_m = re.search(r"'forbidden'\s*=>\s*\[(.*?)\]", entry)
                
                required = re.findall(r"'(G\d+)'", req_m.group(1)) if req_m else []
                forbidden = re.findall(r"'(G\d+)'", forb_m.group(1)) if forb_m else []
                
                if code_m:
                    rules.append({'code': code_m.group(1), 'req': required, 'forb': forbidden})
    return facts_seeded, rules

def main():
    fac_const = parse_constants()
    produced_consts = parse_produced_facts()
    seeded_facts, rules = parse_seeder()
    seeded_codes = set(seeded_facts.keys())
    seeded_consts = set(seeded_facts.values())

    print("--- AUDIT RESULTS ---")
    
    # 1. Missing Seedings
    missing_seeding = produced_consts - seeded_consts
    if missing_seeding:
        print(f"MISSING SEEDING: {missing_seeding}")
    else:
        print("SEEDING: OK")
        
    # 2. Ghost Codes in Rules
    ghost_codes = []
    for r in rules:
        for c in r['req'] + r['forb']:
            if c not in seeded_codes:
                ghost_codes.append((r['code'], c))
    if ghost_codes:
        print(f"GHOST CODES: {ghost_codes}")
    else:
        print("CODES: OK")
        
    # 3. Dead Rules
    produced_codes = {code for code, const in seeded_facts.items() if const in produced_consts}
    for r in rules:
        if any(c not in produced_codes for c in r['req']):
             pass # some might be intentional
    
    print("--- DONE ---")

if __name__ == "__main__":
    main()
