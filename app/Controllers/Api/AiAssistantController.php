<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class AiAssistantController extends ResourceController
{
    private $dentistryIssues = [
        // -----------------------
        // Tooth Decay / Cavity
        // -----------------------
        [
            'problem' => 'Tooth Decay / Cavity',
            'keywords' => ['cavity', 'decay', 'hole in tooth', 'tooth pain', 'black spot', 'tooth damage', 'sugar cavity', 'tooth rot'],
            'treatment' => 'Filling, Fluoride treatment',
            'lab_tests' => 'X-ray, Oral exam',
            'reason' => 'Caused by bacterial plaque, sugary diet, poor oral hygiene'
        ],
        [
            'problem' => 'Gum Disease / Gingivitis',
            'keywords' => ['bleeding gums', 'swollen gums', 'gum pain', 'red gums', 'gum inflammation', 'tender gums', 'gum bleeding while brushing'],
            'treatment' => 'Deep cleaning, Scaling, Antibiotics',
            'lab_tests' => 'Periodontal exam',
            'reason' => 'Plaque buildup causing inflammation'
        ],
        [
            'problem' => 'Periodontitis',
            'keywords' => ['advanced gum disease', 'bone loss', 'loose teeth', 'gum infection', 'pocket gums', 'gum recession', 'gum detachment'],
            'treatment' => 'Scaling, Root planing, Surgery',
            'lab_tests' => 'Periodontal charting, X-ray',
            'reason' => 'Advanced gum infection leading to bone loss'
        ],
        [
            'problem' => 'Tooth Sensitivity',
            'keywords' => ['sensitive teeth', 'pain hot', 'pain cold', 'pain sweet', 'ice cream tooth pain', 'hot drink pain', 'sharp tooth pain'],
            'treatment' => 'Desensitizing toothpaste, Fluoride varnish',
            'lab_tests' => 'Oral exam',
            'reason' => 'Exposed dentin or enamel erosion'
        ],
        [
            'problem' => 'Bad Breath / Halitosis',
            'keywords' => ['bad breath', 'stinky mouth', 'smelly breath', 'halitosis', 'morning breath', 'rotten smell in mouth', 'foul odor from teeth'],
            'treatment' => 'Deep cleaning, Oral hygiene, Mouthwash',
            'lab_tests' => 'Oral exam, Bacterial test',
            'reason' => 'Poor hygiene, bacterial buildup, or systemic issues'
        ],
        [
            'problem' => 'Tooth Abscess',
            'keywords' => ['tooth abscess', 'pus', 'swelling', 'severe tooth pain', 'gum pus', 'tooth infection', 'pain on biting'],
            'treatment' => 'Root canal, Extraction, Antibiotics',
            'lab_tests' => 'X-ray, Oral exam',
            'reason' => 'Bacterial infection at tooth root'
        ],
        [
            'problem' => 'Impacted Wisdom Teeth',
            'keywords' => ['wisdom teeth pain', 'jaw pain', 'swollen gums', 'impacted tooth', 'wisdom tooth', 'teeth not erupting', 'pain back molar'],
            'treatment' => 'Extraction',
            'lab_tests' => 'X-ray',
            'reason' => 'Teeth blocked by jawbone or gums'
        ],
        [
            'problem' => 'Cracked / Fractured Tooth',
            'keywords' => ['cracked tooth', 'broken tooth', 'chipped tooth', 'fracture', 'splintered tooth', 'tooth chip', 'pain on chewing'],
            'treatment' => 'Crown, Filling, Root canal',
            'lab_tests' => 'Oral exam, X-ray',
            'reason' => 'Trauma or biting hard foods'
        ],
        [
            'problem' => 'Malocclusion / Misaligned Teeth',
            'keywords' => ['crooked teeth', 'overbite', 'underbite', 'misaligned bite', 'crossbite', 'jaw misalignment', 'crowded teeth'],
            'treatment' => 'Orthodontic braces',
            'lab_tests' => 'Orthodontic exam',
            'reason' => 'Genetic factors or jaw growth issues'
        ],
        [
            'problem' => 'Teeth Grinding / Bruxism',
            'keywords' => ['grinding', 'jaw pain', 'headaches', 'clenching teeth', 'tooth wear', 'night grinding', 'teeth knocking'],
            'treatment' => 'Night guard, Stress management',
            'lab_tests' => 'Oral exam',
            'reason' => 'Stress or misaligned bite'
        ],
        [
            'problem' => 'Oral Ulcers / Canker Sores',
            'keywords' => ['ulcers', 'canker sores', 'mouth sores', 'painful mouth', 'white patches', 'open sore', 'mouth lesion'],
            'treatment' => 'Topical gel, Avoid irritants',
            'lab_tests' => 'Oral exam',
            'reason' => 'Stress, nutritional deficiencies, or trauma'
        ],
        [
            'problem' => 'Tooth Erosion',
            'keywords' => ['enamel loss', 'acid wear', 'sensitive teeth', 'worn teeth', 'tooth thinning', 'tooth wear', 'erosion from soda'],
            'treatment' => 'Fluoride varnish, Protective measures',
            'lab_tests' => 'Oral exam',
            'reason' => 'Acidic diet, GERD, or brushing habits'
        ],
        [
            'problem' => 'Stained / Discolored Teeth',
            'keywords' => ['yellow teeth', 'stains', 'dark teeth', 'discolored teeth', 'coffee stains', 'tea stains', 'smoking stains'],
            'treatment' => 'Whitening, Cleaning',
            'lab_tests' => 'Oral exam',
            'reason' => 'Diet, smoking, or poor hygiene'
        ],
        [
            'problem' => 'Dry Mouth / Xerostomia',
            'keywords' => ['dry mouth', 'sticky mouth', 'thirst', 'parched mouth', 'saliva deficiency', 'mouth dryness', 'cotton mouth'],
            'treatment' => 'Hydration, Saliva substitutes',
            'lab_tests' => 'Oral exam, Blood test',
            'reason' => 'Medications, dehydration, or salivary gland issues'
        ],
        [
            'problem' => 'Temporomandibular Joint Disorders (TMJ)',
            'keywords' => ['jaw pain', 'clicking jaw', 'difficulty opening mouth', 'TMJ', 'jaw popping', 'jaw stiffness', 'jaw locking'],
            'treatment' => 'Jaw exercises, Night guard',
            'lab_tests' => 'Oral exam, MRI',
            'reason' => 'Jaw misalignment, stress, or arthritis'
        ],
        [
            'problem' => 'Oral Cancer (early detection)',
            'keywords' => ['oral lesions', 'lumps', 'persistent sores', 'mouth cancer', 'oral tumor', 'tongue lesion', 'non-healing sore'],
            'treatment' => 'Biopsy, Surgery, Radiotherapy',
            'lab_tests' => 'Biopsy, Oral exam',
            'reason' => 'Genetic factors, tobacco, alcohol'
        ],
        [
            'problem' => 'Tooth Loss / Missing Teeth',
            'keywords' => ['missing tooth', 'gap', 'broken tooth', 'lost tooth', 'extracted tooth', 'empty socket'],
            'treatment' => 'Dentures, Implants, Bridges',
            'lab_tests' => 'Oral exam, X-ray',
            'reason' => 'Trauma, decay, or periodontal disease'
        ],
        [
            'problem' => 'Sensitive Gums / Gingival Recession',
            'keywords' => ['receding gums', 'gum pain', 'root exposure', 'sensitive gums', 'gum shrinkage', 'exposed root'],
            'treatment' => 'Scaling, Surgery',
            'lab_tests' => 'Oral exam',
            'reason' => 'Brushing technique, age, gum disease'
        ],
        [
            'problem' => 'Plaque / Tartar Build-up',
            'keywords' => ['plaque', 'tartar', 'yellow deposit', 'hard plaque', 'soft plaque', 'calculus', 'sticky teeth'],
            'treatment' => 'Cleaning, Scaling',
            'lab_tests' => 'Oral exam',
            'reason' => 'Poor oral hygiene'
        ],
        [
            'problem' => 'Dry Socket',
            'keywords' => ['post-extraction pain', 'bad taste', 'dry socket', 'tooth extraction pain', 'alveolar osteitis'],
            'treatment' => 'Pain management, Irrigation',
            'lab_tests' => 'Oral exam',
            'reason' => 'Improper healing after tooth extraction'
        ],
        [
            'problem' => 'Oral Infection / Stomatitis',
            'keywords' => ['mouth infection', 'swelling', 'pain', 'stomatitis', 'oral inflammation', 'mouth redness', 'throat infection'],
            'treatment' => 'Antibiotics, Antiseptic rinse',
            'lab_tests' => 'Oral swab, Blood test',
            'reason' => 'Bacterial or viral infection'
        ],
        [
            'problem' => 'Pediatric Dental Issues',
            'keywords' => ['baby teeth', 'teething pain', 'early cavities', 'child tooth pain', 'milk teeth', 'deciduous teeth issues'],
            'treatment' => 'Filling, Fluoride treatment, Teething gel',
            'lab_tests' => 'Oral exam, X-ray',
            'reason' => 'Diet, hygiene, developmental issues'
        ],
        [
            'problem' => 'Cosmetic Dentistry Issues',
            'keywords' => ['crooked teeth', 'discolored teeth', 'chipped teeth', 'uneven smile', 'gaps', 'yellow teeth'],
            'treatment' => 'Veneers, Whitening, Bonding, Orthodontics',
            'lab_tests' => 'Oral exam',
            'reason' => 'Aesthetic or trauma-related issues'
        ],
        [
            'problem' => 'Dental Trauma / Injuries',
            'keywords' => ['broken tooth', 'chipped tooth', 'tooth knocked out', 'jaw injury', 'cut gums', 'mouth trauma'],
            'treatment' => 'Immediate dental care, Stabilization, Root canal or extraction',
            'lab_tests' => 'Oral exam, X-ray',
            'reason' => 'Accident or impact injuries'
        ],
        [
            'problem' => 'Jaw / Facial Pain',
            'keywords' => ['jaw pain', 'face pain', 'TMJ pain', 'jaw swelling', 'facial ache', 'jaw stiffness'],
            'treatment' => 'Pain management, Jaw exercises, Night guard',
            'lab_tests' => 'Oral exam, MRI',
            'reason' => 'TMJ disorders, trauma, or infection'
        ],
        [
            'problem' => 'Enamel Hypoplasia',
            'keywords' => ['enamel defect', 'weak enamel', 'thin enamel', 'yellow spots', 'enamel pits'],
            'treatment' => 'Fluoride varnish, Dental sealants',
            'lab_tests' => 'Oral exam, X-ray',
            'reason' => 'Genetic or developmental enamel defect'
        ],
        [
            'problem' => 'Hyperdontia / Extra Teeth',
            'keywords' => ['extra teeth', 'supernumerary teeth', 'additional teeth', 'crowded teeth', 'misaligned extra tooth'],
            'treatment' => 'Extraction, Orthodontics',
            'lab_tests' => 'Panoramic X-ray',
            'reason' => 'Developmental anomaly'
        ],
        [
            'problem' => 'Hypodontia / Missing Teeth',
            'keywords' => ['missing tooth', 'absent tooth', 'gap', 'tooth agenesis'],
            'treatment' => 'Implants, Bridges, Orthodontics',
            'lab_tests' => 'X-ray, Oral exam',
            'reason' => 'Genetic or developmental issue'
        ],
        [
            'problem' => 'Dental Fluorosis',
            'keywords' => ['fluorosis', 'white spots', 'stained enamel', 'mottled teeth'],
            'treatment' => 'Microabrasion, Whitening, Veneers',
            'lab_tests' => 'Oral exam',
            'reason' => 'Excessive fluoride exposure during development'
        ],
        [
            
            'problem' => 'Impacted Canines',
            'keywords' => ['impacted canine', 'delayed eruption', 'tooth not emerging', 'upper canine impacted'],
            'treatment' => 'Surgical exposure, Orthodontics',
            'lab_tests' => 'X-ray, CBCT',
            'reason' => 'Insufficient space or obstruction'
        ],
        [
            'problem' => 'Odontoma',
            'keywords' => ['odontoma', 'tooth tumor', 'tooth growth', 'benign growth', 'jaw mass'],
            'treatment' => 'Surgical removal',
            'lab_tests' => 'X-ray, Biopsy',
            'reason' => 'Developmental benign tumor of odontogenic origin'
        ],
        [
             'problem' => 'Cleft Lip / Palate',
        'keywords' => ['cleft lip', 'cleft palate', 'open roof', 'facial cleft', 'feeding difficulty'],
        'treatment' => 'Surgery, Speech therapy, Orthodontics',
        'lab_tests' => 'Oral exam, Imaging',
        'reason' => 'Congenital malformation'
        ],
        [
               'problem' => 'Dental Trauma / Injury',
        'keywords' => ['chipped tooth', 'broken tooth', 'knocked out tooth', 'tooth injury', 'tooth fracture'],
        'treatment' => 'Bonding, Crown, Root canal, Extraction',
        'lab_tests' => 'X-ray, Oral exam',
        'reason' => 'Accident, fall, or sports injury'
        ],
        [
             'problem' => 'Pulpitis / Inflamed Pulp',
        'keywords' => ['pulp inflammation', 'toothache', 'sensitive tooth', 'tooth pulp pain'],
        'treatment' => 'Root canal, Pulp capping',
        'lab_tests' => 'Pulp vitality test, X-ray',
        'reason' => 'Bacterial infection or decay reaching pulp'
        ],
        [
            
        'problem' => 'Frenulum Problems / Tongue-tie',
        'keywords' => ['tongue tie', 'tight frenulum', 'restricted tongue', 'speech difficulty', 'feeding issue'],
        'treatment' => 'Frenectomy',
        'lab_tests' => 'Oral exam',
        'reason' => 'Short lingual or labial frenulum restricting movement'

        ],
        [
            'problem' => 'Pericoronitis',
        'keywords' => ['swollen gum', 'painful gum', 'partially erupted tooth', 'infection around tooth'],
        'treatment' => 'Antibiotics, Cleaning, Extraction',
        'lab_tests' => 'Oral exam, X-ray',
        'reason' => 'Infection around partially erupted tooth'
        ],
        [
            
        'problem' => 'Aphthous Ulcers',
        'keywords' => ['canker sores', 'mouth ulcers', 'painful sores', 'white patches', 'recurrent ulcers'],
        'treatment' => 'Topical corticosteroids, Pain relief gels',
        'lab_tests' => 'Oral exam',
        'reason' => 'Stress, immune response, or nutritional deficiency'
        ],
        [
             'problem' => 'Halitosis from Tongue Coating',
        'keywords' => ['bad breath', 'tongue coating', 'smelly tongue', 'morning breath', 'halitosis'],
        'treatment' => 'Tongue cleaning, Mouthwash',
        'lab_tests' => 'Oral exam, Bacterial test',
        'reason' => 'Bacteria buildup on tongue surface'
        ],
        [
            'problem' => 'Dental Cysts',
        'keywords' => ['cyst', 'jaw cyst', 'tooth cyst', 'swelling', 'radiolucent lesion'],
        'treatment' => 'Surgical removal',
        'lab_tests' => 'X-ray, CBCT, Biopsy',
        'reason' => 'Developmental or inflammatory cysts around teeth'
        ],
        [
            'problem' => 'Eruption Hematoma',
        'keywords' => ['eruption cyst', 'gum swelling', 'bluish gum', 'tooth eruption'],
        'treatment' => 'Monitoring, Surgical drainage if needed',
        'lab_tests' => 'Oral exam',
        'reason' => 'Fluid-filled cyst over erupting tooth'
        ],
        [
            
        'problem' => 'Gingival Hyperplasia',
        'keywords' => ['gum overgrowth', 'swollen gums', 'thick gums', 'enlarged gums', 'gingival enlargement'],
        'treatment' => 'Scaling, Gingivectomy',
        'lab_tests' => 'Oral exam, Biopsy',
        'reason' => 'Medications, inflammation, or genetics'
        ],
        [
                'problem' => 'Periapical Abscess',
        'keywords' => ['tooth abscess', 'pus', 'severe pain', 'swelling', 'infection at root'],
        'treatment' => 'Root canal, Extraction, Antibiotics',
        'lab_tests' => 'X-ray, Oral exam',
        'reason' => 'Bacterial infection at tooth apex'
        ],
        [
            'problem' => 'Tooth Ankylosis',
        'keywords' => ['fused tooth', 'immobile tooth', 'tooth stuck', 'bone fused tooth'],
        'treatment' => 'Extraction, Orthodontic intervention',
        'lab_tests' => 'X-ray',
        'reason' => 'Fusion of tooth root to bone preventing eruption'
        ],
        [
            'problem' => 'Oral Lichen Planus',
        'keywords' => ['white patches', 'mouth lesions', 'burning sensation', 'red spots', 'striae'],
        'treatment' => 'Topical corticosteroids, Pain relief gels',
        'lab_tests' => 'Oral exam, Biopsy',
        'reason' => 'Chronic inflammatory autoimmune disorder'
        ],
         // -----------------------
        // Additional 30+ problems can be added below similarly
        // -----------------------
                                  
        
    ];

    public function getSuggestion()
    {
        $data = $this->request->getJSON(true);
        $symptoms = strtolower($data['symptoms'] ?? '');

        if (!$symptoms) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Symptoms required'
            ], 400);
        }

        $matched = [];

        foreach ($this->dentistryIssues as $issue) {
            foreach ($issue['keywords'] as $keyword) {
                if (strpos($symptoms, strtolower($keyword)) !== false) {
                    $matched[] = [
                        'problem' => $issue['problem'],
                        'treatment' => $issue['treatment'],
                        'lab_tests' => $issue['lab_tests'],
                        'reason' => $issue['reason']
                    ];
                    break;
                }
            }
        }

        if (empty($matched)) {
            return $this->respond([
                'status' => 'success',
                'symptoms' => $symptoms,
                'suggestion' => 'No matching dental issue found. Please consult a dentist.'
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'symptoms' => $symptoms,
            'suggestion' => $matched
        ]);
    }
}
