<?php
/**
 * Prompt Builder
 * Constructs AI prompts from questionnaire data
 */

class PromptBuilder {
    
    /**
     * Build character description prompt from questionnaire answers
     */
    public function buildCharacterPrompt($data) {
        $prompt = "Create a mysterious masked character profile based on these answers:\n\n";
        
        // Chapter 2: Masked Identity (Questions 6-10)
        if (isset($data['chapter02'])) {
            $ch2 = $data['chapter02'];
            
            if (isset($ch2['6'])) {
                $prompt .= "ANIMAL REPRESENTATION: " . $ch2['6'] . "\n";
            }
            if (isset($ch2['7'])) {
                $prompt .= "COSTUME COLOR: " . $ch2['7'] . "\n";
            }
            if (isset($ch2['8'])) {
                $prompt .= "NATURAL ELEMENT: " . $this->getElementText($ch2['8']) . "\n";
            }
            if (isset($ch2['9'])) {
                $prompt .= "MASK DESIGN: " . $ch2['9'] . "\n";
            }
            if (isset($ch2['10'])) {
                $prompt .= "ENTRANCE MUSIC STYLE: " . $ch2['10'] . "\n";
            }
        }
        
        // Chapter 3: Personality (Questions 11-16)
        if (isset($data['chapter03'])) {
            $ch3 = $data['chapter03'];
            
            if (isset($ch3['11'])) {
                $prompt .= "\nSUPERPOWER: " . $ch3['11'] . "\n";
            }
            if (isset($ch3['15'])) {
                $prompt .= "LIFE AS NETFLIX SERIES: " . $ch3['15'] . "\n";
            }
        }
        
        // Chapter 8: Color personality (Question 40)
        if (isset($data['chapter08']['40'])) {
            $prompt .= "PERSONALITY COLOR: " . $data['chapter08']['40'] . "\n";
        }
        
        $prompt .= "\nCreate a 150-250 word character description that is:\n";
        $prompt .= "- Mysterious and intriguing\n";
        $prompt .= "- Anonymous (no real identity clues)\n";
        $prompt .= "- Vivid and cinematic\n";
        $prompt .= "- Gives them a creative character name/alias\n";
        $prompt .= "- TV gameshow quality\n";
        
        return $prompt;
    }
    
    /**
     * Build environment description prompt
     */
    public function buildEnvironmentPrompt($data) {
        $prompt = "Create an atmospheric environment description for this character based on:\n\n";
        
        // Chapter 6: Fantasy & Dreams (Questions 27-31)
        if (isset($data['chapter06'])) {
            $ch6 = $data['chapter06'];
            
            if (isset($ch6['27'])) {
                $prompt .= "FICTIONAL WORLD PREFERENCE: " . $ch6['27'] . "\n";
            }
            if (isset($ch6['30'])) {
                $prompt .= "DREAM RESTAURANT CONCEPT: " . $ch6['30'] . "\n";
            }
            if (isset($ch6['31'])) {
                $prompt .= "BUCKET LIST DESTINATION: " . $ch6['31'] . "\n";
            }
        }
        
        // Chapter 2: Element (for atmosphere)
        if (isset($data['chapter02']['8'])) {
            $prompt .= "ELEMENT ASSOCIATION: " . $this->getElementText($data['chapter02']['8']) . "\n";
        }
        
        $prompt .= "\nCreate a 100-150 word environment description that:\n";
        $prompt .= "- Sets a cinematic atmosphere\n";
        $prompt .= "- Includes sensory details (sights, sounds, atmosphere)\n";
        $prompt .= "- Matches the character's essence\n";
        $prompt .= "- Is dramatic and TV-quality\n";
        
        return $prompt;
    }
    
    /**
     * Build props list prompt
     */
    public function buildPropsPrompt($data, $characterDescription) {
        $prompt = "Create 3-5 signature props/items for this character:\n\n";
        $prompt .= "CHARACTER: " . substr($characterDescription, 0, 300) . "...\n\n";
        
        // Chapter 4: Verborgen Talenten (Questions 17-21)
        if (isset($data['chapter04'])) {
            $ch4 = $data['chapter04'];
            
            if (isset($ch4['18'])) {
                $prompt .= "UNUSUAL HOBBY/COLLECTION: " . $ch4['18'] . "\n";
            }
            if (isset($ch4['19'])) {
                $prompt .= "UNEXPECTED MUSICAL SKILL: " . $ch4['19'] . "\n";
            }
        }
        
        // Chapter 8: Tattoo idea (Question 39)
        if (isset($data['chapter08']['39'])) {
            $prompt .= "HYPOTHETICAL TATTOO: " . $data['chapter08']['39'] . "\n";
        }
        
        $prompt .= "\nCreate props that:\n";
        $prompt .= "- Have symbolic meaning\n";
        $prompt .= "- Are visually distinctive\n";
        $prompt .= "- Help identify the character\n";
        $prompt .= "- Are memorable and unique\n";
        $prompt .= "\nFormat: Numbered list with brief description of each prop's significance.\n";
        
        return $prompt;
    }
    
    /**
     * Build video story prompt for specific level
     */
    public function buildVideoStoryPrompt($data, $level) {
        $prompt = "Create a compelling video story prompt for LEVEL {$level}:\n\n";
        
        switch ($level) {
            case 1: // Surface level
                if (isset($data['chapter03']['13'])) {
                    $prompt .= "DINNER GUEST CHOICE: " . $data['chapter03']['13'] . "\n";
                }
                if (isset($data['chapter03']['14'])) {
                    $prompt .= "CONVERSATION TOPIC: " . $data['chapter03']['14'] . "\n";
                }
                $prompt .= "\nCreate a 50-100 word prompt for a 30-60 second video about a public achievement or known passion.\n";
                break;
                
            case 2: // Hidden depths
                if (isset($data['chapter04']['17'])) {
                    $prompt .= "SECRET TALENT: " . $data['chapter04']['17'] . "\n";
                }
                if (isset($data['chapter08']['37'])) {
                    $prompt .= "SECRET MUSIC GENRE: " . $data['chapter08']['37'] . "\n";
                }
                $prompt .= "\nCreate a 50-100 word prompt for a 60-90 second video revealing a surprising hidden side.\n";
                break;
                
            case 3: // Deep secrets
                if (isset($data['chapter05']['22'])) {
                    $prompt .= "CHILDHOOD DREAM: " . $data['chapter05']['22'] . "\n";
                }
                if (isset($data['chapter05']['26'])) {
                    $prompt .= "ADVICE TO 16-YEAR-OLD SELF: " . $data['chapter05']['26'] . "\n";
                }
                $prompt .= "\nCreate a 50-100 word prompt for a 90-120 second video about transformation and vulnerability.\n";
                break;
        }
        
        $prompt .= "The prompt should be emotionally engaging and specific, starting with 'As [character name]...'\n";
        
        return $prompt;
    }
    
    /**
     * Build image generation prompt for character
     */
    public function buildCharacterImagePrompt($data, $characterDescription) {
        $prompt = "Professional character portrait for TV gameshow. ";
        
        // Extract key visual elements
        if (isset($data['chapter02'])) {
            $ch2 = $data['chapter02'];
            
            if (isset($ch2['6'])) {
                // Extract animal
                preg_match('/\b(wolf|leeuw|eagle|owl|fox|dolphin|bear|cat|dog|paard|hond|kat)\b/i', $ch2['6'], $animal);
                if (!empty($animal)) {
                    $prompt .= "Symbolism of " . $animal[0] . ". ";
                }
            }
            
            if (isset($ch2['7'])) {
                $prompt .= "Costume in " . $ch2['7'] . ". ";
            }
            
            if (isset($ch2['9'])) {
                $prompt .= "Mask featuring " . $ch2['9'] . ". ";
            }
        }
        
        // Add character description excerpt
        $prompt .= substr($characterDescription, 0, 200) . ". ";
        
        // Technical requirements
        $prompt .= "Centered composition, dramatic studio lighting, mysterious masked figure, ";
        $prompt .= "professional photography, 4K quality, TV production standard, cinematic.";
        
        return $prompt;
    }
    
    /**
     * Build image generation prompt for environment
     */
    public function buildEnvironmentImagePrompt($data, $environmentDescription) {
        $prompt = "Cinematic environment scene. ";
        
        // Add environment description
        $prompt .= $environmentDescription . " ";
        
        // Technical requirements
        $prompt .= "Atmospheric lighting, professional photography, wide angle, ";
        $prompt .= "4K quality, dramatic mood, TV production quality.";
        
        return $prompt;
    }
    
    /**
     * System prompts for different generation types
     */
    public function getCharacterSystemPrompt() {
        return "You are a creative character designer for a TV gameshow called 'The Masked Employee'. " .
               "Your specialty is creating mysterious, anonymous character personas that are intriguing and TV-worthy. " .
               "Create vivid descriptions that protect anonymity while being engaging and memorable. " .
               "Always give characters a creative alias/name.";
    }
    
    public function getEnvironmentSystemPrompt() {
        return "You are a world-builder specializing in atmospheric environments for TV productions. " .
               "Create cinematic, sensory-rich environment descriptions that match character personalities. " .
               "Focus on mood, atmosphere, and visual details that would look great on screen.";
    }
    
    public function getPropsSystemPrompt() {
        return "You are a prop designer for TV gameshow character development. " .
               "Create signature items that are symbolic, visually distinctive, and help identify characters. " .
               "Each prop should have meaning and be memorable.";
    }
    
    public function getVideoStorySystemPrompt($level) {
        $prompts = [
            1 => "You are a storytelling coach creating prompts for surface-level, public video stories. " .
                 "These are safe, shareable stories about achievements and known passions.",
            2 => "You are a storytelling coach creating prompts for mid-depth video stories. " .
                 "These reveal surprising, hidden sides and unexpected talents.",
            3 => "You are a storytelling coach creating prompts for deep, vulnerable video stories. " .
                 "These explore transformation, challenges overcome, and authentic truth."
        ];
        
        return $prompts[$level] ?? $prompts[1];
    }
    
    /**
     * Helper: Convert element index to text
     */
    private function getElementText($index) {
        $elements = [
            0 => 'Fire - passionate and energetic',
            1 => 'Water - calm and adaptive',
            2 => 'Earth - stable and reliable',
            3 => 'Air - free and creative'
        ];
        
        return $elements[$index] ?? 'Unknown element';
    }
}

?>
