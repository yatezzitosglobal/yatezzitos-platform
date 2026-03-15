# 📚 Knowledge Bases (RAG) — Yatezzitos

Bases de conocimiento segmentadas por destino para el Agente Marina. Cada archivo contiene el catálogo de embarcaciones de un destino específico, optimizado para < 25,000 caracteres.

## Archivos

| Destino | Archivo | Chars |
|---|---|---|
| Cancún | `marina_rag_cancun.md` | ~22,380 |
| Puerto Vallarta | `marina_rag_puerto_vallarta.md` | ~23,790 |
| Los Cabos | `marina_rag_yates_los_cabos.md` | ~19,710 |
| Mazatlán | `marina_rag_mazatlan.md` | ~14,454 |
| La Paz | `marina_rag_la_paz.md` | ~13,336 |
| Acapulco | `marina_rag_yates_acapulco.md` | ~9,356 |
| Playa del Carmen | `marina_rag_yates_playa_del_carmen.md` | ~8,486 |
| Nuevo Vallarta | `marina_rag_yates_en_nuevo_vallarta.md` | ~6,609 |
| Ixtapa | `marina_rag_yates_ixtapa.md` | ~4,793 |
| Huatulco | `marina_rag_yates_huatulco.md` | ~3,994 |

## Distribución en GoHighLevel

| Knowledge Base en GHL | Destinos |
|---|---|
| **Mazatlan, Acapulco, Cancun, Ixtapa y Huatulco** | Mazatlán, Acapulco, Cancún, Ixtapa, Huatulco |
| **Cabos, Vallarta, Playa, N. Vta y La Paz** | Los Cabos, Puerto Vallarta, Playa del Carmen, Nuevo Vallarta, La Paz |

## Regeneración

Para regenerar estos archivos desde Google Sheets:
```bash
cd scripts/yatezzitos_crawler
source venv/bin/activate
python generate_md.py
```

---

**Última actualización:** 15 de marzo 2026
