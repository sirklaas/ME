class MaskedEmployeeForm {
    constructor() {
        this.questionsData = null;
        this.currentChapter = 0;
        this.playerName = '';
        this.answers = {};
        this.totalChapters = 0;
        this.gameName = '';
        this.gamenameLoaded = false; // Track if dynamic gamename is loaded
        this.currentLanguage = 'nl'; // Default to Dutch
        this.confidentialityAccepted = false;
        this.aiSummary = '';
        this.userEmail = '';
        this.characterPreview = ''; // Short character preview
        this.characterDescription = ''; // Real AI character description
        this.worldDescription = ''; // Real AI world description
        this.characterName = ''; // Extracted character name
        this.imagePrompt = ''; // AI image prompt
        this.imageUrl = ''; // Generated image URL
        this.characterType = ''; // User-selected character type (from Question 7)
        this.department = ''; // User's department (from Question 6)
        this.translations = this.initTranslations();
        this.testAnswers = null; // Test data
        
        this.init();
    }
    
    initTranslations() {
        return {
            nl: {
                // Language Selection Page
                languageHeading: '🎭 THE MASKED EMPLOYEE 🎭',
                languageSubheading: 'Kies je taal / Choose your language',
                letsDoThis: 'Laten we beginnen!',
                // Welcome Page
                welcomeTitle: 'ONTDEK DE MYSTERIEUZE HELD IN JEZELF!',
                welcomeSubheading: 'The Masked Employee gaat beginnen en\nWIJ ZOEKEN JOUW VERBORGEN SUPERKRACHT!',
                welcomeBody1: 'Dit is geen gewone vragenlijst - dit is jouw kans om te schitteren als de mysterieuze held die je altijd al was.',
                welcomeBody2: 'Vergeet alles wat je denkt over "normaal doen" op kantoor.',
                welcomeBody3: 'Het is de hoogste tijd om die gekke, creatieve, onverwachte kant van jezelf te laten zien.',
                welcomeBody4: 'Ben jij thuis misschien operazanger, eet je stiekum pizza met ananas,',
                welcomeBody5: 'ken jij als enige alle stiekem alle K-pop choreografieën uit je hoofd?',
                excitementTitle: 'DEZE VRAGENLIJST is JOUW TICKET NAAR ROEM!',
                bullet1: 'Geef antwoorden die je collega\'s\nin hun koffie laten verslikken van verbazing',
                bullet2: 'Denk buiten alle hokjes - hoe gekker, hoe beter!',
                bullet3: 'Dit is JOUW moment om te laten zien\nwie je werkelijk bent',
                bullet4: 'Maak er een feestje van - neem de tijd ervoor\nen geniet van elke vraag!',
                startButton: 'JA, START DE VRAGENLIJST!',
                gameNameLabel: 'Gameshow:',
                playerNameLabel: 'Je Naam:',
                playerNamePlaceholder: 'Vul je volledige naam in...',
                // Navigation
                prevButton: '← Vorige',
                nextButton: 'Volgende →',
                stepOf: 'van',
                // Confidentiality
                confidentialityTitle: '⚠️ ABSOLUTE GEHEIMHOUDING VERPLICHT ⚠️',
                agreementText: 'Ja, ik ga volledig akkoord en zweer dit absoluut geheim te houden voor alles en iedereen.',
                modalAcceptButton: 'Ik ga akkoord',
                // Summary Page
                // Preview Page
                previewTitle: '🎭 We hebben je karakter gevonden!',
                previewSubtitle: 'Op basis van je antwoorden hebben we dit unieke karakter voor je gecreëerd:',
                loadingPreview: 'AI creëert je karakter...',
                regenerateButton: '🔄 Genereer opnieuw',
                acceptButton: '✅ Ja, dit ben ik!',
                previewNote: 'Je krijgt straks een gedetailleerde beschrijving met omgeving, en props.',
                // Summary Page
                summaryTitle: '📋 Jouw Karakter Samenvatting',
                confirmationQuestion: 'Is deze samenvatting correct?',
                redoText: '🔄 Ik wil dit nog een keer doen',
                confirmText: '✅ Ja dit is helemaal goed',
                finalSubmitButton: 'Bevestigen',
                aiAnalyzing: 'AI analyseert je antwoorden...',
                // Processing Page
                processingTitle: 'Je afbeelding wordt gegenereerd!',
                processingThankYou: 'Bedankt',
                processingDescription: 'Je antwoorden zijn opgeslagen en we genereren nu je unieke karakter afbeelding.',
                processingWhatNow: '🎭 Wat gebeurt er nu?',
                processingStep1: '✅ Je antwoorden zijn opgeslagen',
                processingStep2: '🎨 AI genereert je karakter afbeelding',
                processingStep3: '📧 Je ontvangt een kopie per email',
                processingStep4: '🔒 Onthoud: absolute geheimhouding blijft van kracht!',
                restartButton: 'Terug naar start',
                // Loading
                loadingText: 'Antwoorden worden opgeslagen...'
            },
            en: {
                // Language Selection Page
                languageHeading: '🎭 THE MASKED EMPLOYEE 🎭',
                languageSubheading: 'Kies je taal / Choose your language',
                letsDoThis: 'Let\'s do this!',
                // Welcome Page
                welcomeTitle: 'DISCOVER THE MYSTERIOUS SUPERHERO IN YOURSELF!',
                welcomeSubheading: 'The Masked Employee is about to begin and\nWE ARE LOOKING FOR YOUR HIDDEN SUPERPOWER!',
                welcomeBody1: 'This is not an ordinary questionnaire - this is your chance to shine as the mysterious hero you\'ve always been.',
                welcomeBody2: 'Forget everything you think about "acting normal" at the office.',
                welcomeBody3: 'It\'s high time to show that crazy, creative, unexpected side of yourself.',
                welcomeBody4: 'Are you perhaps an opera singer at home, do you secretly eat pizza with pineapple,',
                welcomeBody5: 'do you know all the K-pop choreographies by heart?',
                excitementTitle: 'THIS QUESTIONNAIRE is YOUR TICKET TO FAME!',
                bullet1: 'Give answers that make your colleagues\nchoke on their coffee in amazement',
                bullet2: 'Think outside all the boxes - the crazier, the better!',
                bullet3: 'This is YOUR moment to show\nwho you really are',
                bullet4: 'Make it a party - take your time\nand enjoy every question!',
                startButton: 'YES, START THE QUESTIONNAIRE!',
                gameNameLabel: 'Gameshow:',
                playerNameLabel: 'Your Name:',
                playerNamePlaceholder: 'Enter your full name...',
                // Navigation
                prevButton: '← Previous',
                nextButton: 'Next →',
                stepOf: 'of',
                // Confidentiality
                confidentialityTitle: '⚠️ ABSOLUTE CONFIDENTIALITY REQUIRED ⚠️',
                confidentialityWarning: 'WARNING: By participating in this questionnaire<br>you agree to the following confidentiality rules:',
                forbiddenRule1: 'Discussing this questionnaire with colleagues',
                forbiddenRule2: 'Sharing your answers with others',
                forbiddenRule3: 'Revealing who is participating in the show',
                forbiddenRule4: 'Discussing what talents or qualities you have revealed',
                forbiddenRule5: 'Giving hints about your identity during the show',
                penaltyTitle: '💰 PENALTY CLAUSE:',
                penaltyText: 'In case of violation of the confidentiality obligation, a fine of €9,750 will be charged.<br>This rule applies to all participants and employees as well as their family and friends.<br>In short, everyone who could have knowledge of the participants list.',
                agreementText: 'Yes, I fully agree and swear to keep this absolutely secret from everyone.',
                modalAcceptButton: 'I agree',
                // Summary Page
                // Preview Page
                previewTitle: '🎭 We found your character!',
                previewSubtitle: 'Based on your answers, we created this unique character for you:',
                loadingPreview: 'AI is creating your character...',
                regenerateButton: '🔄 No, please Regenerate',
                acceptButton: '✅ Yes, this is me!',
                previewNote: 'You will receive a detailed description with environment, props and stories shortly.',
                // Summary Page
                summaryTitle: '📋 Your Character Summary',
                confirmationQuestion: 'Is this summary correct?',
                redoText: '🔄 I want to do this again',
                confirmText: '✅ Yes, this is completely correct',
                finalSubmitButton: 'Confirm',
                aiAnalyzing: 'AI is analyzing your answers...',
                // Processing Page
                processingTitle: 'Your image is being generated!',
                processingThankYou: 'Thank you',
                processingDescription: 'Your answers have been saved and we are now generating your unique character image.',
                processingWhatNow: '🎭 What happens now?',
                processingStep1: '✅ Your answers have been saved',
                processingStep2: '🎨 AI generates your character image',
                processingStep3: '📧 You will receive a copy by email',
                processingStep4: '🔒 Remember: absolute confidentiality remains in effect!',
                restartButton: 'Back to start',
                // Loading
                loadingText: 'Saving answers...'
            }
        };
    }

    async init() {
        console.log('🚀 STARTING FORM INITIALIZATION...');
        try {
            console.log('📋 Step 1: Loading config...');
            await this.loadConfig();
            console.log('✅ Config loaded successfully');
            
            console.log('📋 Step 2: Loading chapters...');
            await this.loadAllChapters();
            console.log('✅ Chapters loaded successfully');
            
            console.log('📋 Step 3: Loading gamename from PocketBase...');
            await this.loadGamename();
            console.log('✅ Gamename loading completed');
            
            console.log('📋 Step 4: Setting up event listeners...');
            this.setupEventListeners();
            console.log('✅ Event listeners set up');
            
            console.log('📋 Step 5: Language selection page ready...');
            // Language selection page is already active in HTML, no need to display anything
            console.log('✅ Language selection page is visible');
            
            console.log('🎉 FORM INITIALIZATION COMPLETE!');
        } catch (error) {
            console.error('💥 FAILED TO INITIALIZE FORM:', error);
            console.error('Error details:', error.stack);
            this.showError('Er is een fout opgetreden bij het laden van de vragenlijst.');
        }
    }

    async loadConfig() {
        try {
            // Load unified questions file with STRONG cache busting
            const timestamp = new Date().getTime();
            const random = Math.random().toString(36).substring(7);
            const configResponse = await fetch(`questions-unified.json?v=${timestamp}&r=${random}&nocache=1`, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });
            console.log('🔄 Loading unified questions with cache busting:', `questions-unified.json?v=${timestamp}&r=${random}&nocache=1`);
            if (!configResponse.ok) {
                throw new Error(`HTTP error! status: ${configResponse.status}`);
            }
            const config = await configResponse.json();
            console.log('📊 Unified questions loaded:', config);
            
            // Store the complete config structure (chapters already included)
            this.questionsData = config;
            this.totalChapters = config.chapters.length;
            console.log(`✅ Loaded ${this.totalChapters} chapters from unified file`);
        } catch (error) {
            console.error('Error loading config:', error);
            throw error;
        }
    }

    async loadAllChapters() {
        // No longer needed - chapters are already loaded in loadConfig()
        // Keeping function for compatibility
        console.log('✅ Chapters already loaded from unified file');
    }

    async loadGamename() {
        console.log('🔍 Starting DYNAMIC PocketBase gamename loading...');
        
        try {
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            console.log('✅ PocketBase instance created');
            
            // Try multiple authentication approaches with your credentials
            let authenticated = false;
            
            // Method 1: Try admin authentication with your credentials
            try {
                console.log('🔐 Attempting admin authentication...');
                // Try different combinations of your credentials
                const credentials = 'biknu8-pyrnaB-mytvyx';
                
                // Try as admin token first
                pb.authStore.save(credentials, { admin: true });
                console.log('✅ Admin token set, testing connection...');
                
                // Test the connection
                const testRecords = await pb.collection('MEQuestions').getFullList({ '$limit': 1 });
                authenticated = true;
                console.log('🎉 Admin authentication successful!');
                
            } catch (adminError) {
                console.log('⚠️ Admin token failed, trying direct auth...');
                
                // Method 2: Try direct API with Authorization header
                try {
                    const response = await fetch('https://pinkmilk.pockethost.io/api/collections/MEQuestions/records', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${credentials}`
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        console.log('🎉 Direct API with Bearer token successful!');
                        
                        if (data.items && data.items.length > 0) {
                            const record = data.items[0];
                            console.log('🎯 Found record via direct API:', record);
                            
                            // Extract gamename from any string field
                            console.log('🔍 All record fields:', Object.keys(record));
                            console.log('📊 Full record data:', record);
                            
                            // Look for specific gamename fields first
                            const priorityFields = ['gamename', 'game_name', 'company_name', 'event_name', 'title', 'name'];
                            let foundGamename = null;
                            
                            for (const field of priorityFields) {
                                if (record[field] && typeof record[field] === 'string') {
                                    foundGamename = record[field];
                                    console.log(`✅ Found gamename in priority field '${field}':`, foundGamename);
                                    break;
                                }
                            }
                            
                            // If no priority field found, look for any meaningful string field
                            if (!foundGamename) {
                                for (const [key, value] of Object.entries(record)) {
                                    if (typeof value === 'string' && 
                                        value.length > 8 && // Longer than typical IDs
                                        !key.toLowerCase().includes('id') && 
                                        !key.includes('created') && 
                                        !key.includes('updated') &&
                                        !value.match(/^[a-z0-9]{15}$/)) { // Avoid PocketBase IDs
                                        foundGamename = value;
                                        console.log(`🎯 Found gamename in field '${key}':`, foundGamename);
                                        break;
                                    }
                                }
                            }
                            
                            if (foundGamename) {
                                this.gameName = foundGamename;
                                this.gamenameLoaded = true; // Mark as dynamically loaded
                                console.log(`🎉 DYNAMIC SUCCESS! Gamename:`, this.gameName);
                                this.updateGameNameDisplay();
                                return; // Exit early on success
                            } else {
                                console.log('❌ No suitable gamename field found');
                            }
                        }
                    } else {
                        console.log('❌ Direct API failed:', response.status);
                        throw new Error(`API failed with status ${response.status}`);
                    }
                    
                } catch (apiError) {
                    console.log('❌ All authentication methods failed:', apiError.message);
                }
            }
            
            // If we got here with authentication, try to fetch records
            if (authenticated) {
                try {
                    const records = await pb.collection('MEQuestions').getFullList({
                        sort: '-created'
                    });
                    
                    console.log('🎉 Authenticated fetch successful!');
                    console.log('📊 Records found:', records.length);
                    
                    if (records && records.length > 0) {
                        const record = records[0];
                        console.log('🎯 Processing latest record:', record);
                        
                        // Look for gamename in any string field
                        console.log('🔍 All authenticated record fields:', Object.keys(record));
                        console.log('📊 Full authenticated record:', record);
                        
                        // Look for specific gamename fields first
                        const priorityFields = ['gamename', 'game_name', 'company_name', 'event_name', 'title', 'name'];
                        let foundGamename = null;
                        
                        for (const field of priorityFields) {
                            if (record[field] && typeof record[field] === 'string') {
                                foundGamename = record[field];
                                console.log(`✅ Found gamename in priority field '${field}':`, foundGamename);
                                break;
                            }
                        }
                        
                        // If no priority field found, look for any meaningful string field
                        if (!foundGamename) {
                            for (const [key, value] of Object.entries(record)) {
                                if (typeof value === 'string' && 
                                    value.length > 8 && // Longer than typical IDs
                                    !key.toLowerCase().includes('id') && 
                                    !key.includes('created') && 
                                    !key.includes('updated') &&
                                    !value.match(/^[a-z0-9]{15}$/)) { // Avoid PocketBase IDs
                                    foundGamename = value;
                                    console.log(`🎯 Found gamename in field '${key}':`, foundGamename);
                                    break;
                                }
                            }
                        }
                        
                        if (foundGamename) {
                            this.gameName = foundGamename;
                            this.gamenameLoaded = true; // Mark as dynamically loaded
                            console.log(`🎉 DYNAMIC SUCCESS! Gamename:`, this.gameName);
                            this.updateGameNameDisplay();
                            return; // Exit early on success
                        }
                    }
                    
                } catch (fetchError) {
                    console.log('❌ Authenticated fetch failed:', fetchError.message);
                }
            }
            
            // If we get here, all methods failed
            console.log('❌❌ ALL AUTHENTICATION METHODS FAILED ❌❌');
            console.log('🚨 CRITICAL: Dynamic gamename loading not working');
            console.log('📝 PocketBase might still be recovering from restart');
            console.log('Will retry in 5 seconds...');
            
            // Show temporary message
            this.gameName = '⏳ PocketBase Connecting... (Retrying)';
            this.updateGameNameDisplay();
            
            // Retry after 5 seconds
            setTimeout(() => {
                console.log('🔄 Retrying PocketBase connection...');
                this.loadGamename();
            }, 5000);
            
        } catch (error) {
            console.error('💥 Critical error in loadGamename:', error);
            this.gameName = '🚨 Error: ' + error.message;
            this.updateGameNameDisplay();
        }
    }

    async saveInitialPlayerData() {
        try {
            console.log('🚀 Creating initial PocketBase record for player...');
            
            // Prepare initial data with player info and empty chapters
            const initialData = {
                gamename: this.gameName,
                nameplayer: this.playerName,
                language: this.currentLanguage, // Save selected language
                current_chapter: 0, // Haven't started any chapter yet
                total_chapters: this.totalChapters,
                progress_percentage: 0,
                started_at: new Date().toISOString(),
                last_updated: new Date().toISOString(),
                status: 'started',
                chapter01: {},
                chapter02: {},
                chapter03: {},
                chapter04: {},
                chapter05: {},
                chapter06: {},
                chapter07: {},
                chapter08: {}
            };
            
            console.log('📊 Initial player data:', {
                player: initialData.nameplayer,
                gamename: initialData.gamename,
                status: initialData.status
            });
            
            // Save to localStorage as backup
            localStorage.setItem('maskedEmployeeInitial', JSON.stringify(initialData));
            
            // PocketBase integration with authentication
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            
            // Use the same authentication as gamename loading
            const credentials = 'biknu8-pyrnaB-mytvyx';
            pb.authStore.save(credentials, { admin: true });
            
            // Check if player already has a record (in case they're restarting)
            const existingRecords = await pb.collection('MEQuestions').getFullList({
                filter: `nameplayer = "${this.playerName}" && gamename = "${this.gameName}"`
            });
            
            let record;
            if (existingRecords.length > 0) {
                // Update existing record (player is restarting)
                record = await pb.collection('MEQuestions').update(existingRecords[0].id, initialData);
                console.log('✅ Updated existing player record (restart):', record.id);
            } else {
                // Create new record for new player
                record = await pb.collection('MEQuestions').create(initialData);
                console.log('✅ Created new player record:', record.id);
            }
            
            // Store the record ID for future updates
            this.playerRecordId = record.id;
            localStorage.setItem('maskedEmployeeRecordId', record.id);
            
            console.log('🎉 Initial player data saved successfully!');
            return record;
            
        } catch (error) {
            console.error('💥 Initial player data save error:', error);
            throw error;
        }
    }

    async saveProgressToPocketBase() {
        try {
            console.log('💾 Starting progressive save to PocketBase...');
            
            // Organize current answers by chapter
            const chapterAnswers = this.organizeAnswersByChapter(this.answers);
            
            // Prepare data for PocketBase with current progress
            const progressData = {
                gamename: this.gameName,
                nameplayer: this.playerName,
                current_chapter: this.currentChapter,
                total_chapters: this.totalChapters,
                progress_percentage: Math.round((this.currentChapter / this.totalChapters) * 100),
                last_updated: new Date().toISOString(),
                status: this.currentChapter === this.totalChapters ? 'completed' : 'in_progress',
                chapter01: chapterAnswers.chapter01 || {},
                chapter02: chapterAnswers.chapter02 || {},
                chapter03: chapterAnswers.chapter03 || {},
                chapter04: chapterAnswers.chapter04 || {},
                chapter05: chapterAnswers.chapter05 || {},
                chapter06: chapterAnswers.chapter06 || {},
                chapter07: chapterAnswers.chapter07 || {},
                chapter08: chapterAnswers.chapter08 || {}
            };
            
            console.log('📊 Progress data to save:', {
                player: progressData.nameplayer,
                chapter: progressData.current_chapter,
                progress: progressData.progress_percentage + '%',
                status: progressData.status,
                answers_count: Object.keys(this.answers).length
            });
            
            // Save to localStorage as backup
            localStorage.setItem('maskedEmployeeProgress', JSON.stringify(progressData));
            
            // PocketBase integration with authentication
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            
            // Use the same authentication as gamename loading
            const credentials = 'biknu8-pyrnaB-mytvyx';
            pb.authStore.save(credentials, { admin: true });
            
            // Try to update existing record using stored record ID or find by player info
            let record;
            try {
                if (this.playerRecordId) {
                    // Update using stored record ID
                    record = await pb.collection('MEQuestions').update(this.playerRecordId, progressData);
                    console.log('✅ Updated existing record using stored ID:', record.id);
                } else {
                    // Fallback: find record by player info
                    const existingRecords = await pb.collection('MEQuestions').getFullList({
                        filter: `nameplayer = "${this.playerName}" && gamename = "${this.gameName}"`
                    });
                    
                    if (existingRecords.length > 0) {
                        record = await pb.collection('MEQuestions').update(existingRecords[0].id, progressData);
                        this.playerRecordId = existingRecords[0].id; // Store for future updates
                        console.log('✅ Updated existing record found by search:', record.id);
                    } else {
                        // Create new record if none found
                        record = await pb.collection('MEQuestions').create(progressData);
                        this.playerRecordId = record.id;
                        console.log('✅ Created new record:', record.id);
                    }
                }
            } catch (pbError) {
                console.error('❌ PocketBase operation failed:', pbError);
                throw new Error(`PocketBase save failed: ${pbError.message}`);
            }
            
            console.log('🎉 Progressive save completed successfully!');
            return record;
            
        } catch (error) {
            console.error('💥 Progressive save error:', error);
            throw error;
        }
    }

    async saveToPocketBase(data, characterData) {
        // Organize answers by chapter to match PocketBase structure
        const chapterAnswers = this.organizeAnswersByChapter(data.answers);
        
        // Prepare data for PocketBase schema with separate chapter fields AND character data
        const submissionData = {
            gamename: this.gameName,
            nameplayer: data.playerName,
            chapter01: chapterAnswers.chapter01 || {},
            chapter02: chapterAnswers.chapter02 || {},
            chapter03: chapterAnswers.chapter03 || {},
            chapter04: chapterAnswers.chapter04 || {},
            chapter05: chapterAnswers.chapter05 || {},
            chapter06: chapterAnswers.chapter06 || {},
            chapter07: chapterAnswers.chapter07 || {},
            chapter08: chapterAnswers.chapter08 || {},
            chapter09: chapterAnswers.chapter09 || {},
            submission_date: data.timestamp,
            total_questions: data.totalQuestions,
            status: 'completed',
            // Add character data fields
            character_name: characterData.character_name || '',
            character_type: characterData.character_type || '',
            personality_traits: characterData.personality_traits || '',
            ai_summary: characterData.ai_summary || '',
            story_prompt1: characterData.story_prompt_level1 || '',
            story_prompt2: characterData.story_prompt_level2 || '',
            story_prompt3: characterData.story_prompt_level3 || '',
            image_generation_prompt: characterData.image_generation_prompt || '',
            character_generation_success: characterData.success || false
        };
        
        try {
            // Simulate API delay
            await new Promise(resolve => setTimeout(resolve, 1500));
            
            // Save to localStorage as backup
            const submissions = JSON.parse(localStorage.getItem('maskedEmployeeSubmissions') || '[]');
            submissions.push(submissionData);
            localStorage.setItem('maskedEmployeeSubmissions', JSON.stringify(submissions));
            
            console.log('💾 Submission data prepared:', submissionData);
            
            // PocketBase integration
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            const credentials = 'biknu8-pyrnaB-mytvyx';
            pb.authStore.save(credentials, { admin: true });
            
            const record = await pb.collection('MEQuestions').create(submissionData);
            console.log('✅ Submission saved to PocketBase:', record);
            
            // Store record ID for image upload
            this.playerRecordId = record.id;
            console.log('📝 Stored playerRecordId:', this.playerRecordId);
            
            return record;
        } catch (error) {
            console.error('❌ PocketBase save error:', error);
            console.error('Error details:', error.response || error.message);
            throw error;
        }
    }

    organizeAnswersByChapter(answers) {
        // Define question ID ranges for each chapter
        const chapterRanges = {
            chapter01: [1, 2, 3, 4, 5],           // Introductie & Basisinformatie
            chapter02: [6, 7, 8, 9, 10],          // Masked Identity
            chapter03: [11, 12, 13, 14, 15, 16],  // Persoonlijke Eigenschappen
            chapter04: [17, 18, 19, 20, 21],      // Verborgen Talenten
            chapter05: [22, 23, 24, 25, 26],      // Jeugd & Verleden
            chapter06: [27, 28, 29, 30, 31],      // Fantasie & Dromen
            chapter07: [32, 33, 34, 35, 36],      // Eigenaardigheden
            chapter08: [37, 38, 39, 40],          // Onverwachte Voorkeuren
            chapter09: [41, 42, 43]               // Film Maken
        };

        const chapterAnswers = {};
        
        // Group answers by chapter
        Object.entries(chapterRanges).forEach(([chapterKey, questionIds]) => {
            const chapterData = {};
            questionIds.forEach(questionId => {
                if (answers[questionId] !== undefined) {
                    chapterData[questionId] = answers[questionId];
                }
            });
            if (Object.keys(chapterData).length > 0) {
                chapterAnswers[chapterKey] = chapterData;
            }
        });

        return chapterAnswers;
    }

    updateLanguage() {
        const t = this.translations[this.currentLanguage];
        
        // Update all translatable elements
        document.getElementById('startButton').textContent = t.startButton;
        document.getElementById('agreementText').textContent = t.agreementText;
        document.getElementById('modalAcceptButton').textContent = t.modalAcceptButton;
        document.getElementById('summaryTitle').textContent = t.summaryTitle;
        document.getElementById('confirmationQuestion').textContent = t.confirmationQuestion;
        document.getElementById('redoText').textContent = t.redoText;
        document.getElementById('confirmText').textContent = t.confirmText;
        document.getElementById('finalSubmitButton').textContent = t.finalSubmitButton;
        document.getElementById('processingTitle').textContent = t.processingTitle;
        
        // Update modal title
        const modalTitle = document.getElementById('modalConfidentialityTitle');
        if (modalTitle) {
            modalTitle.textContent = t.confidentialityTitle;
        }
        
        // Update player name label
        const playerNameLabel = document.querySelector('label[for="playerName"]');
        if (playerNameLabel) {
            playerNameLabel.textContent = t.playerNameLabel;
        }
        
        const playerNameInput = document.getElementById('playerName');
        if (playerNameInput) {
            playerNameInput.placeholder = t.playerNamePlaceholder;
        }
    }
    
    showConfidentialityModal() {
        // Player name already validated and stored from language selection page
        if (!this.playerName || !this.validatePlayerName(this.playerName)) {
            this.showError(this.currentLanguage === 'nl' ? 
                'Er is een fout opgetreden. Naam niet gevonden.' : 
                'An error occurred. Name not found.');
            return;
        }
        
        // Update modal content with translations
        const t = this.translations[this.currentLanguage];
        
        // Update title
        document.getElementById('modalConfidentialityTitle').textContent = t.confidentialityTitle;
        
        // Update warning text (if English, use translated version)
        if (this.currentLanguage === 'en') {
            document.getElementById('modalConfidentialityWarning').innerHTML = t.confidentialityWarning;
            
            // Update forbidden rules list
            const modalForbiddenList = document.getElementById('modalForbiddenRules');
            modalForbiddenList.innerHTML = '';
            [t.forbiddenRule1, t.forbiddenRule2, t.forbiddenRule3, t.forbiddenRule4, t.forbiddenRule5].forEach(rule => {
                const li = document.createElement('li');
                li.textContent = rule;
                modalForbiddenList.appendChild(li);
            });
            
            // Update penalty clause
            document.getElementById('modalPenaltyClause').innerHTML = `<strong>${t.penaltyTitle}</strong><br>${t.penaltyText}`;
        }
        // Dutch content is hardcoded in HTML, no need to update
        
        // Reset checkbox and button
        document.getElementById('confidentialityAgree').checked = false;
        document.getElementById('modalAcceptButton').disabled = true;
        
        // Show modal
        document.getElementById('confidentialityModal').classList.add('show');
    }
    
    hideConfidentialityModal() {
        document.getElementById('confidentialityModal').classList.remove('show');
    }
    
    async showSummaryPage() {
        document.getElementById('chapterPage').style.display = 'none';
        document.getElementById('summaryPage').style.display = 'block';
        
        // Hide progress indicators
        document.getElementById('stepIndicator').style.display = 'none';
        document.getElementById('progressContainer').style.display = 'none';
        
        // Show loading state
        document.querySelector('.loading-summary').style.display = 'flex';
        document.getElementById('summaryContent').style.display = 'none';
        
        // Reset confirmation options
        document.querySelectorAll('input[name="confirmation"]').forEach(radio => {
            radio.checked = false;
        });
        document.getElementById('finalSubmitButton').disabled = true;
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Generate AI summary
        await this.generateAISummary();
    }
    
    async generateAISummary() {
        try {
            // Prepare answers for AI analysis
            const formattedAnswers = this.formatAnswersForAI();
            
            console.log('🤖 Generating AI summary...');
            
            // Create timeout promise (10 seconds)
            const timeout = new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Request timeout')), 10000)
            );
            
            // Call backend PHP endpoint for OpenAI integration
            const fetchPromise = fetch('generate-character-summary.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    answers: formattedAnswers,
                    playerName: this.playerName,
                    language: this.currentLanguage
                })
            });
            
            // Race between fetch and timeout
            const response = await Promise.race([fetchPromise, timeout]);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Failed to generate summary');
            }
            
            this.aiSummary = result.summary;
            
            console.log('✅ AI summary generated successfully');
            
            // Display summary
            document.querySelector('.loading-summary').style.display = 'none';
            const summaryContent = document.getElementById('summaryContent');
            summaryContent.innerHTML = result.summary;
            summaryContent.style.display = 'block';
            
        } catch (error) {
            console.error('❌ Error generating AI summary:', error.message);
            
            // Fallback to mock summary if API fails
            console.log('📝 Using fallback mock summary...');
            const formattedAnswers = this.formatAnswersForAI();
            const summary = this.createMockSummary(formattedAnswers);
            this.aiSummary = summary;
            
            document.querySelector('.loading-summary').style.display = 'none';
            const summaryContent = document.getElementById('summaryContent');
            summaryContent.innerHTML = summary;
            summaryContent.style.display = 'block';
            
            console.log('✅ Mock summary displayed');
        }
    }
    
    formatAnswersForAI() {
        const formatted = {};
        this.questionsData.chapters.forEach((chapter, index) => {
            const lang = this.currentLanguage;
            chapter.questions.forEach(question => {
                const answer = this.answers[question.id];
                if (answer !== undefined) {
                    const questionText = question.question[lang] || question.question;
                    const answerText = question.type === 'multiple-choice' ? 
                        (question.options[lang] ? question.options[lang][answer] : question.options[answer]) : answer;
                    formatted[questionText] = answerText;
                }
            });
        });
        return formatted;
    }
    
    createMockSummary(answers) {
        // This is a placeholder. In production, this should call OpenAI API
        if (this.currentLanguage === 'nl') {
            return `
<div class="character-section">
<h3>🎭 Karakter Persona</h3>
<p><strong>Karakter Naam:</strong> De Stille Innovator</p>
<p><strong>Beschrijving:</strong> Een mysterieuze figuur die werkt in de schaduwen van creativiteit, waar anderen slechts oppervlakkige patronen zien. De Stille Innovator draagt een donkere cape met subtiele lichtgevende patronen die alleen zichtbaar zijn in het donker, symboliserend verborgen diepte en onverwachte helderheid. Met een kalme maar indringende blik en een verzameling vintage instrumenten, transformeert dit karakter alledaagse momenten in buitengewone verhalen. Bekend om zachte woorden die grote impact hebben, en om oplossingen te vinden waar anderen alleen problemen zien.</p>
</div>

<div class="environment-section">
<h3>🌍 Kenmerkende Omgeving</h3>
<p>Een verlicht atelier op zolder waar creativiteit en technologie samenkomen. Houten balken kruisen het plafond, met tussen de balken slingers van vintage lampen die een warm, gouden licht werpen. Overal liggen notitieboeken met schetsen en ideeën, naast moderne tablets en apparatuur. Door de grote daklichten stroomt natuurlijk licht naar binnen, terwijl zachte jazz-muziek op de achtergrond speelt. De geur van vers gezette koffie en oude boeken hangt in de lucht, met hier en daar een vleugje van avontuur en mogelijkheden.</p>
</div>

<div class="props-section">
<h3>✨ Signature Props</h3>
<ul>
<li><strong>Vintage Lederen Notitieboek:</strong> Gevuld met hand-getekende mindmaps en dromen die werkelijkheid werden</li>
<li><strong>Bronzen Kompas:</strong> Wijst niet naar het noorden, maar naar nieuwe mogelijkheden en onontdekte paden</li>
<li><strong>Collectie Oude Sleutels:</strong> Elke sleutel representeert een opgelost mysterie of ontgrendelde potentie</li>
<li><strong>Draadloze Koptelefoon met Vintage Aesthetic:</strong> Brug tussen moderne technologie en tijdloze wijsheid</li>
</ul>
</div>

<div class="story-prompts-section">
<h3>🎬 Video Story Prompts</h3>

<div class="story-level-1">
<h4>Level 1: Oppervlakte Verhaal (30-60 sec)</h4>
<p><strong>Prompt:</strong> "Als De Stille Innovator, vertel over het moment dat je een project realiseerde dat niemand voor mogelijk hield. Wat was het idee, welke obstakels overwon je, en wat was de reactie toen het succesvol was? Eindig met wat deze ervaring je leerde over doorzettingsvermogen."</p>
</div>

<div class="story-level-2">
<h4>Level 2: Verborgen Dieptes (60-90 sec)</h4>
<p><strong>Prompt:</strong> "Onthul een verborgen talent of passie die je collega's nooit hebben gezien. Hoe ontdekte je dit talent? Waarom houd je het verborgen in je professionele leven? Deel een moment waarop deze verborgen kant van jezelf bijna aan het licht kwam. Eindig met waarom deze dualiteit belangrijk voor je is."</p>
</div>

<div class="story-level-3">
<h4>Level 3: Diepste Geheimen (90-120 sec)</h4>
<p><strong>Prompt:</strong> "Vertel het verhaal van het moment dat je leven fundamenteel veranderde - een moment van verlies, transformatie, of onverwachte helderheid. Beschrijf hoe je je voelde voor dat moment, wat er gebeurde, en wie je daarna werd. Deel de kwetsbaarheid en kracht die je uit die ervaring haalde. Sluit af met de levensles die je nog steeds elke dag gebruikt."</p>
</div>
</div>

<p style="margin-top: 30px;"><em>Deze samenvatting is gegenereerd op basis van je ${Object.keys(this.answers).length} antwoorden. Dit is een voorbeeld - met OpenAI API krijg je een volledig gepersonaliseerd profiel.</em></p>
            `;
        } else {
            return `
<div class="character-section">
<h3>🎭 Character Persona</h3>
<p><strong>Character Name:</strong> The Silent Innovator</p>
<p><strong>Description:</strong> A mysterious figure who works in the shadows of creativity, where others see only surface patterns. The Silent Innovator wears a dark cape with subtle luminescent patterns only visible in darkness, symbolizing hidden depth and unexpected clarity. With a calm but penetrating gaze and a collection of vintage instruments, this character transforms everyday moments into extraordinary stories. Known for quiet words that carry great impact, and for finding solutions where others see only problems.</p>
</div>

<div class="environment-section">
<h3>🌍 Signature Environment</h3>
<p>An illuminated attic studio where creativity and technology converge. Wooden beams cross the ceiling, with strings of vintage lamps between them casting warm, golden light. Notebooks with sketches and ideas lie everywhere, next to modern tablets and equipment. Natural light streams through large skylights, while soft jazz music plays in the background. The scent of freshly brewed coffee and old books hangs in the air, with hints of adventure and possibilities.</p>
</div>

<div class="props-section">
<h3>✨ Signature Props</h3>
<ul>
<li><strong>Vintage Leather Notebook:</strong> Filled with hand-drawn mind maps and dreams that became reality</li>
<li><strong>Bronze Compass:</strong> Points not to north, but to new possibilities and undiscovered paths</li>
<li><strong>Collection of Old Keys:</strong> Each key represents a solved mystery or unlocked potential</li>
<li><strong>Wireless Headphones with Vintage Aesthetic:</strong> Bridge between modern technology and timeless wisdom</li>
</ul>
</div>

<div class="story-prompts-section">
<h3>🎬 Video Story Prompts</h3>

<div class="story-level-1">
<h4>Level 1: Surface Story (30-60 sec)</h4>
<p><strong>Prompt:</strong> "As The Silent Innovator, tell about the moment you realized a project that no one thought possible. What was the idea, which obstacles did you overcome, and what was the reaction when it succeeded? End with what this experience taught you about perseverance."</p>
</div>

<div class="story-level-2">
<h4>Level 2: Hidden Depths (60-90 sec)</h4>
<p><strong>Prompt:</strong> "Reveal a hidden talent or passion your colleagues have never seen. How did you discover this talent? Why do you keep it hidden in your professional life? Share a moment when this hidden side of you almost came to light. End with why this duality is important to you."</p>
</div>

<div class="story-level-3">
<h4>Level 3: Deepest Secrets (90-120 sec)</h4>
<p><strong>Prompt:</strong> "Tell the story of the moment your life fundamentally changed - a moment of loss, transformation, or unexpected clarity. Describe how you felt before that moment, what happened, and who you became afterwards. Share the vulnerability and strength you drew from that experience. Close with the life lesson you still use every day."</p>
</div>
</div>

<p style="margin-top: 30px;"><em>This summary was generated based on your ${Object.keys(this.answers).length} answers. This is an example - with OpenAI API you'll get a fully personalized profile.</em></p>
            `;
        }
    }
    
    async handleFinalSubmission() {
        const selectedOption = document.querySelector('input[name="confirmation"]:checked');
        
        if (!selectedOption) {
            return;
        }
        
        if (selectedOption.value === 'redo') {
            // User wants to redo - go back to second chapter
            if (confirm(this.currentLanguage === 'nl' ? 
                'Weet je zeker dat je opnieuw wilt beginnen vanaf hoofdstuk 2?' : 
                'Are you sure you want to restart from chapter 2?')) {
                this.currentChapter = 2;
                document.getElementById('summaryPage').style.display = 'none';
                document.getElementById('chapterPage').style.display = 'block';
                document.getElementById('stepIndicator').style.display = 'block';
                document.getElementById('progressContainer').style.display = 'flex';
                this.displayChapter(this.currentChapter);
            }
        } else {
            // User confirmed - show email modal
            this.showEmailModal();
        }
    }

    showEmailModal() {
        const modal = document.getElementById('emailModal');
        const emailInput = document.getElementById('emailInput');
        
        // Update modal text based on language
        const lang = this.currentLanguage;
        document.getElementById('emailModalTitle').textContent = 
            lang === 'nl' ? '📧 Waar mogen we de uitkomst naar toe mailen?' : '📧 Where can we send the results?';
        document.getElementById('sendEmailButtonText').textContent = 
            lang === 'nl' ? 'Verstuur' : 'Send';
        emailInput.placeholder = lang === 'nl' ? 'jouw@email.nl' : 'your@email.com';
        
        // Clear previous email
        emailInput.value = '';
        
        // Show modal
        modal.style.display = 'flex';
        
        // Focus on email input
        setTimeout(() => emailInput.focus(), 300);
    }

    closeEmailModal() {
        document.getElementById('emailModal').style.display = 'none';
    }

    async handleEmailSubmit() {
        const emailInput = document.getElementById('emailInput');
        const email = emailInput.value.trim();
        
        // Validate email
        if (!email || !this.isValidEmail(email)) {
            alert(this.currentLanguage === 'nl' ? 
                'Vul een geldig e-mailadres in' : 
                'Please enter a valid email address');
            emailInput.focus();
            return;
        }
        
        // Store email
        this.userEmail = email;
        
        // Close modal
        this.closeEmailModal();
        
        // Show loading
        this.showLoading(true);
        
        try {
            // Send initial email with character description
            console.log('📧 Sending initial email with character data...');
            await this.sendDescriptionEmail();
            console.log('✅ Initial email sent');
            
            // Show processing page
            this.showProcessingPage();
            
            // Start image generation with stored character data
            console.log('🎨 Starting image generation with email:', email);
            if (this.currentCharacterData) {
                this.generateAndUploadImage(this.currentCharacterData).catch(err => {
                    console.error('❌ Image generation failed:', err);
                });
            } else {
                console.warn('⚠️ No character data available for image generation');
            }
            
        } catch (error) {
            console.error('❌ Error in email/image flow:', error);
            alert(this.currentLanguage === 'nl' ? 
                'Er is een fout opgetreden. Controleer de console voor details.' :
                'An error occurred. Check the console for details.');
        } finally {
            this.showLoading(false);
        }
    }

    async sendDescriptionEmail() {
        try {
            console.log('📧 Sending email with character data...');
            
            // Use new character data structure
            const characterData = this.currentCharacterData || {};
            
            const response = await fetch('https://www.pinkmilk.eu/ME/send-description-email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: this.userEmail,
                    playerName: this.playerName,
                    gameName: this.gameName,
                    language: this.currentLanguage,
                    characterDescription: characterData.ai_summary || '',
                    worldDescription: '', // Not used in new flow
                    characterName: characterData.character_name || 'Your Character'
                })
            });

            const result = await response.json();
            
            if (result.success) {
                console.log('✅ Description email sent to:', this.userEmail);
            } else {
                console.warn('⚠️ Email send failed:', result.error);
            }
            
        } catch (error) {
            console.error('❌ Error sending description email:', error);
            // Don't throw - continue with image generation
        }
    }

    async generateCharacterImage() {
        try {
            console.log('🎨 Generating character image via Leonardo.ai...');
            
            // Use the image_generation_prompt from character data
            const characterData = this.currentCharacterData || {};
            const imagePrompt = characterData.image_generation_prompt;
            
            if (!imagePrompt) {
                throw new Error('No image generation prompt available');
            }
            
            console.log('📝 Using image prompt:', imagePrompt.substring(0, 150) + '...');
            
            // Timeout after 60 seconds (image generation takes time)
            const timeout = new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Image generation timeout')), 60000)
            );
            
            // Call Leonardo.ai API via PHP wrapper
            const fetchPromise = fetch('https://www.pinkmilk.eu/ME/generate-image-leonardo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    prompt: imagePrompt,
                    playerName: this.playerName
                })
            });
            
            const response = await Promise.race([fetchPromise, timeout]);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                const errorMsg = result.message || result.error || 'Failed to generate image';
                console.error('❌ Server error:', errorMsg);
                if (result.details) {
                    console.error('Details:', result.details);
                }
                throw new Error(errorMsg);
            }
            
            // Store image prompt
            this.imagePrompt = result.image_prompt;
            
            console.log('✅ Image generation response received');
            console.log('📝 Prompt used:', this.imagePrompt);
            console.log('🔍 Has image_data:', !!result.image_data);
            console.log('🔍 Has image_binary:', !!result.image_binary);
            console.log('🔍 Has image_url:', !!result.image_url);
            
            // Check if we have any image data
            const imageData = result.image_data || result.image_binary;
            if (!imageData && !result.image_url) {
                throw new Error('No image data received from server');
            }
            
            // If we have a direct URL, use it
            if (result.image_url && !imageData) {
                console.log('✅ Using direct image URL:', result.image_url);
                this.imageUrl = result.image_url;
                await this.sendFinalEmailWithImage();
                this.showProcessingPageWithImage();
                return;
            }
            
            // Convert base64 to blob for PocketBase upload
            console.log('📤 Converting image to blob...');
            const imageBlob = this.base64ToBlob(imageData, 'image/jpeg');
            
            // STEP 5: Upload image to PocketBase
            console.log('📤 Uploading image to PocketBase...');
            await this.uploadImageToPocketBase(imageBlob);
            
            console.log('✅ Image uploaded, URL:', this.imageUrl);
            
            // STEP 5: Send final email with image
            await this.sendFinalEmailWithImage();
            
            // Show completion page
            this.showProcessingPageWithImage();
            
        } catch (error) {
            console.error('❌ Error generating image:', error.message);
            
            // Show completion without image
            alert(this.currentLanguage === 'nl' ? 
                'Je karakter is opgeslagen! De afbeelding wordt later gegenereerd.' : 
                'Your character is saved! The image will be generated later.');
            
            this.showProcessingPage();
        }
    }

    base64ToBlob(base64, mimeType = 'image/jpeg') {
        // Remove data URL prefix if present
        const base64Data = base64.replace(/^data:image\/\w+;base64,/, '');
        
        // Decode base64
        const byteCharacters = atob(base64Data);
        const byteArrays = [];
        
        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
            const slice = byteCharacters.slice(offset, offset + 512);
            const byteNumbers = new Array(slice.length);
            
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }
            
            const byteArray = new Uint8Array(byteNumbers);
            byteArrays.push(byteArray);
        }
        
        return new Blob(byteArrays, { type: mimeType });
    }

    async uploadImageToPocketBase(imageBlob) {
        try {
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            const credentials = 'biknu8-pyrnaB-mytvyx';
            pb.authStore.save(credentials, { admin: true });
            
            if (!this.playerRecordId) {
                throw new Error('No player record ID found');
            }
            
            // First, save the image_prompt to PocketBase (before upload)
            console.log('📝 Saving image_prompt to PocketBase first...');
            
            // Structure prompt as JSON for reusability
            const promptData = {
                base_template: "Professional character portrait for TV gameshow",
                character_name: this.characterName,
                full_prompt: this.imagePrompt,
                generated_at: new Date().toISOString(),
                language: this.currentLanguage
            };
            
            await pb.collection('MEQuestions').update(this.playerRecordId, {
                image_prompt: JSON.stringify(promptData),
                email: this.userEmail
            });
            
            // Create form data for file upload
            const formData = new FormData();
            const filename = `character_${this.playerRecordId}_${Date.now()}.jpg`;
            formData.append('image', imageBlob, filename);
            formData.append('status', 'completed_with_image');
            formData.append('completed_at', new Date().toISOString());
            
            console.log('📤 Uploading image file (blob size:', imageBlob.size, 'bytes)');
            
            // Upload to PocketBase
            console.log('📤 Uploading to PocketBase record:', this.playerRecordId);
            const record = await pb.collection('MEQuestions').update(this.playerRecordId, formData);
            
            // Get the image URL from PocketBase
            if (record.image) {
                this.imageUrl = pb.files.getUrl(record, record.image);
                console.log('✅ Image uploaded to PocketBase:', this.imageUrl);
            } else {
                throw new Error('Image upload succeeded but no URL returned');
            }
            
        } catch (error) {
            console.error('❌ Error uploading image to PocketBase:', error);
            throw error;
        }
    }

    async sendFinalEmailWithImage() {
        try {
            console.log('📧 Sending final email with image...');
            
            const response = await fetch('https://www.pinkmilk.eu/ME/send-final-email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: this.userEmail,
                    playerName: this.playerName,
                    gameName: this.gameName,
                    language: this.currentLanguage,
                    characterDescription: this.characterDescription,
                    worldDescription: this.worldDescription,
                    characterName: this.characterName,
                    imageUrl: this.imageUrl
                })
            });

            const result = await response.json();
            
            if (result.success) {
                console.log('✅ Final email with image sent!');
            } else {
                console.warn('⚠️ Final email send failed:', result.error);
            }
            
        } catch (error) {
            console.error('❌ Error sending final email:', error);
            // Don't throw - image is generated
        }
    }

    showProcessingPageWithImage() {
        document.getElementById('summaryPage').style.display = 'none';
        document.getElementById('processingPage').style.display = 'block';
        document.getElementById('processingPlayerName').textContent = this.playerName;
        
        // Add image display if available
        const imageContainer = document.getElementById('generatedImageContainer');
        if (imageContainer && this.imageUrl) {
            imageContainer.innerHTML = `
                <div class="generated-image-display">
                    <h3>🎨 ${this.currentLanguage === 'nl' ? 'Jouw Karakter Afbeelding' : 'Your Character Image'}</h3>
                    <img src="${this.imageUrl}" alt="Generated Character" class="character-image" />
                </div>
            `;
            imageContainer.style.display = 'block';
        }
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    async startImageGeneration() {
        this.showLoading(true);
        
        try {
            // Save final data with AI summary to PocketBase
            await this.saveFinalSubmission();
            
            // Send confirmation email
            await this.sendCompletionEmail();
            
            // TODO: Trigger image generation process
            
            this.showLoading(false);
            this.showProcessingPage();
            
        } catch (error) {
            console.error('Error starting image generation:', error);
            this.showLoading(false);
            alert(this.currentLanguage === 'nl' ? 
                'Er is een fout opgetreden. Probeer het opnieuw.' : 
                'An error occurred. Please try again.');
        }
    }

    async sendCompletionEmail() {
        try {
            console.log('📧 Sending completion email...');
            console.log('📧 To:', this.userEmail);
            console.log('📧 Admin CC: klaas@pinkmilk.eu');
            
            const response = await fetch('send-completion-email.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: this.userEmail,
                    playerName: this.playerName,
                    gameName: this.gameName,
                    language: this.currentLanguage
                })
            });

            const result = await response.json();
            
            if (result.success) {
                console.log('✅ User email sent to:', this.userEmail);
                if (result.adminEmailSent) {
                    console.log('✅ Admin email sent to: klaas@pinkmilk.eu');
                } else {
                    console.warn('⚠️ Admin email failed - check server logs');
                    console.warn('⚠️ Note:', result.note);
                }
            } else {
                console.warn('⚠️ Email send failed:', result.error);
                // Don't throw error - continue anyway
            }
            
        } catch (error) {
            console.error('❌ Error sending email:', error);
            // Don't throw error - continue anyway
        }
    }
    
    async saveFinalSubmission() {
        try {
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            const credentials = 'biknu8-pyrnaB-mytvyx';
            pb.authStore.save(credentials, { admin: true });
            
            // Extract character data from AI summary
            const characterData = this.extractCharacterData(this.aiSummary);
            
            const finalData = {
                gamename: this.gameName,
                nameplayer: this.playerName,
                email: this.userEmail || '',
                ai_summary: this.aiSummary,
                character_name: characterData.characterName,
                character_description: characterData.characterDescription,
                environment_description: characterData.environmentDescription,
                props: characterData.props,
                story_prompt_level1: characterData.storyPrompt1,
                story_prompt_level2: characterData.storyPrompt2,
                story_prompt_level3: characterData.storyPrompt3,
                status: 'completed_with_confirmation',
                completed_at: new Date().toISOString(),
                language: this.currentLanguage
            };
            
            if (this.playerRecordId) {
                await pb.collection('MEQuestions').update(this.playerRecordId, finalData);
            }
            
            console.log('✅ Final submission saved successfully');
            console.log('📊 Character data:', characterData);
            
        } catch (error) {
            console.error('❌ Error saving final submission:', error);
            throw error;
        }
    }
    
    extractCharacterData(htmlSummary) {
        // Create a temporary div to parse HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlSummary;
        
        // Extract character name
        const characterNameElement = tempDiv.querySelector('.character-section strong');
        const characterName = characterNameElement ? 
            characterNameElement.nextSibling.textContent.trim() : 
            'Unknown Character';
        
        // Extract character description
        const characterDescElement = tempDiv.querySelector('.character-section p:nth-of-type(2)');
        const characterDescription = characterDescElement ? 
            characterDescElement.textContent.replace('Beschrijving:', '').replace('Description:', '').trim() : 
            '';
        
        // Extract environment description
        const envElement = tempDiv.querySelector('.environment-section p');
        const environmentDescription = envElement ? envElement.textContent.trim() : '';
        
        // Extract props
        const propsElements = tempDiv.querySelectorAll('.props-section li');
        const props = Array.from(propsElements).map(li => li.textContent.trim());
        
        // Extract story prompts
        const story1 = tempDiv.querySelector('.story-level-1 p');
        const story2 = tempDiv.querySelector('.story-level-2 p');
        const story3 = tempDiv.querySelector('.story-level-3 p');
        
        return {
            characterName: characterName,
            characterDescription: characterDescription,
            environmentDescription: environmentDescription,
            props: props,
            storyPrompt1: story1 ? story1.textContent.replace('Prompt:', '').trim() : '',
            storyPrompt2: story2 ? story2.textContent.replace('Prompt:', '').trim() : '',
            storyPrompt3: story3 ? story3.textContent.replace('Prompt:', '').trim() : ''
        };
    }
    
    showProcessingPage() {
        document.getElementById('summaryPage').style.display = 'none';
        document.getElementById('processingPage').style.display = 'block';
        document.getElementById('processingPlayerName').textContent = this.playerName;
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    showCompletionPage(characterData) {
        // Show character preview page first (new 2-step approach)
        this.showCharacterPreviewPage(characterData);
    }

    async showCharacterPreviewPage(characterData) {
        console.log('🎭 Showing character preview page with data:', characterData);
        
        // Hide chapter page, show preview page
        document.getElementById('chapterPage').style.display = 'none';
        document.getElementById('characterPreviewPage').style.display = 'block';
        
        // Hide progress indicators
        document.getElementById('stepIndicator').style.display = 'none';
        document.getElementById('progressContainer').style.display = 'none';
        
        // Reset preview page
        document.querySelector('.loading-preview').style.display = 'block';
        document.getElementById('previewContent').style.display = 'none';
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // If we already have character data, display it directly
        if (characterData && characterData.success) {
            console.log('✅ Using pre-generated character data');
            this.displayCharacterData(characterData);
        } else {
            // Fallback: Generate character preview (old method)
            console.log('⚠️ No character data, using fallback generation');
            await this.generateCharacterPreview();
        }
    }

    async generateCharacterPreview() {
        try {
            const formattedAnswers = this.formatAnswersForAI();
            
            console.log('🤖 Generating REAL character + world description...');
            
            // Timeout after 30 seconds (AI needs more time)
            const timeout = new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Request timeout')), 30000)
            );
            
            const fetchPromise = fetch('generate-character.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    answers: formattedAnswers,
                    playerName: this.playerName,
                    gameName: this.gameName
                })
            });
            
            const response = await Promise.race([fetchPromise, timeout]);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Failed to generate character');
            }
            
            // New endpoint returns different structure - use displayCharacterData
            this.displayCharacterData(result);
            
            console.log('✅ Character generated via fallback');
            console.log('Character Name:', result.character_name);
            
        } catch (error) {
            console.error('❌ Error generating character descriptions:', error.message);
            
            // Fallback to mock
            console.log('📝 Using fallback mock descriptions...');
            const lang = this.currentLanguage;
            this.characterDescription = this.getMockCharacterDescription(lang);
            this.worldDescription = this.getMockWorldDescription(lang);
            
            const previewHTML = this.formatPreviewDisplay(this.characterDescription, this.worldDescription);
            
            document.querySelector('.loading-preview').style.display = 'none';
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = previewHTML;
            previewContent.style.display = 'block';
            
            console.log('✅ Mock descriptions displayed');
        }
    }

    displayCharacterData(characterData) {
        console.log('📺 Displaying character data:', characterData);
        
        // Hide loading, show content
        document.querySelector('.loading-preview').style.display = 'none';
        const previewContent = document.getElementById('previewContent');
        
        const html = `
            <div class="character-preview">
                <div class="character-section">
                    <h3>🎭 ${characterData.character_name || 'Your Character'}</h3>
                    <p><strong>${this.currentLanguage === 'nl' ? 'Type' : 'Type'}:</strong> ${characterData.character_type || 'N/A'}</p>
                    <p><strong>${this.currentLanguage === 'nl' ? 'Persoonlijkheid' : 'Personality'}:</strong><br>${characterData.personality_traits || 'N/A'}</p>
                </div>
                <div class="world-section">
                    <h3>📖 ${this.currentLanguage === 'nl' ? 'AI Samenvatting' : 'AI Summary'}</h3>
                    <p>${characterData.ai_summary || 'N/A'}</p>
                </div>
                ${characterData.story_prompt_level1 ? `
                <div class="story-section">
                    <h3>🎬 ${this.currentLanguage === 'nl' ? 'Verhaal Prompts' : 'Story Prompts'}</h3>
                    <p><strong>${this.currentLanguage === 'nl' ? 'Video 1 (Subtiel)' : 'Video 1 (Subtle)'}:</strong><br>${characterData.story_prompt_level1}</p>
                    <p><strong>${this.currentLanguage === 'nl' ? 'Video 2 (Meer hints)' : 'Video 2 (More clues)'}:</strong><br>${characterData.story_prompt_level2}</p>
                    <p><strong>${this.currentLanguage === 'nl' ? 'Video 3 (Onthulling)' : 'Video 3 (Reveal)'}:</strong><br>${characterData.story_prompt_level3}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        previewContent.innerHTML = html;
        previewContent.style.display = 'block';
    }

    formatPreviewDisplay(characterDesc, worldDesc) {
        return `
            <div class="character-preview">
                <div class="character-section">
                    <h3>🎭 ${this.currentLanguage === 'nl' ? 'Jouw Karakter' : 'Your Character'}</h3>
                    <p>${characterDesc}</p>
                </div>
                <div class="world-section">
                    <h3>🌍 ${this.currentLanguage === 'nl' ? 'Jouw Wereld' : 'Your World'}</h3>
                    <p>${worldDesc}</p>
                </div>
            </div>
        `;
    }

    getMockCharacterDescription(lang) {
        if (lang === 'nl') {
            return "🎭 De Stille Wolf\n\nJe bent een fantastisch enthousiaste wolf die rondloopt in de saaie gangen van een kantoor in Birmingham. Overdag ben je gecamoufleerd in corporate grijs, maar je wilde geest huilt onder de oppervlakte. Met scherpe, intelligente ogen die alles observeren en een verzameling vintage instrumenten, transformeer je alledaagse momenten in buitengewone verhalen.";
        } else {
            return "🎭 The Silent Wolf\n\nYou are a fantastically enthusiastic wolf prowling the dull corridors of a Birmingham office. By day, you're camouflaged in corporate grey, but your wild spirit howls beneath the surface. With sharp, intelligent eyes that observe everything and a collection of vintage instruments, you transform everyday moments into extraordinary stories.";
        }
    }

    getMockWorldDescription(lang) {
        if (lang === 'nl') {
            return "🌍 Een verlicht atelier op zolder waar creativiteit en technologie samenkomen. Houten balken kruisen het plafond, met tussen de balken slingers van vintage lampen die een warm, gouden licht werpen. Door de grote daklichten stroomt maanlicht naar binnen, terwijl zachte jazz-muziek op de achtergrond speelt.";
        } else {
            return "🌍 An illuminated attic studio where creativity and technology converge. Wooden beams cross the ceiling, with strings of vintage lamps casting warm, golden light. Moonlight streams through large skylights, while soft jazz music plays in the background.";
        }
    }

    createMockPreview() {
        if (this.currentLanguage === 'nl') {
            return '<div class="character-preview"><h3>🎭 De Stille Wolf</h3><p>Je bent een fantastisch enthousiaste wolf die rondloopt in de saaie gangen van een kantoor in Birmingham. Overdag ben je gecamoufleerd in corporate grijs, maar je wilde geest huilt onder de oppervlakte. Je leeft voor late-nacht creatieve uitbarstingen en droomt stiekem van vrij rondrennen door maanverlichte bossen.</p></div>';
        } else {
            return '<div class="character-preview"><h3>🎭 The Silent Wolf</h3><p>You are a fantastically enthusiastic wolf prowling the dull corridors of a Birmingham office. By day, you\'re camouflaged in corporate grey, but your wild spirit howls beneath the surface. You live for late-night creative bursts and secretly dream of running free through moonlit forests.</p></div>';
        }
    }

    async regenerateCharacter() {
        console.log('🔄 Regenerating character with variation...');
        
        // Reset and show loading
        document.querySelector('.loading-preview').style.display = 'block';
        document.getElementById('previewContent').style.display = 'none';
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        try {
            // Prepare submission data
            const submissionData = {
                timestamp: new Date().toISOString(),
                playerName: this.playerName,
                answers: this.answers,
                totalQuestions: Object.keys(this.answers).length,
                regenerate: true  // Flag to add variation
            };
            
            // Generate new character with variation
            const characterData = await this.generateCharacterData(submissionData);
            console.log('✅ New character generated:', characterData);
            
            // Display the new character
            if (characterData && characterData.success) {
                this.displayCharacterData(characterData);
            } else {
                throw new Error('Character generation failed');
            }
        } catch (error) {
            console.error('❌ Regeneration error:', error);
            alert(this.currentLanguage === 'nl' ? 
                'Er ging iets mis bij het regenereren. Probeer het opnieuw.' : 
                'Something went wrong while regenerating. Please try again.');
            document.querySelector('.loading-preview').style.display = 'none';
        }
    }

    async acceptCharacterAndContinue() {
        console.log('✅ Character accepted!');
        
        // Hide preview page
        document.getElementById('characterPreviewPage').style.display = 'none';
        
        // Show email modal
        this.showEmailModal();
    }

    async saveDescriptionsToPocketBase() {
        try {
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            const credentials = 'biknu8-pyrnaB-mytvyx';
            pb.authStore.save(credentials, { admin: true });
            
            // Extract character name from description
            const characterName = this.extractCharacterName(this.characterDescription);
            
            // Create ai_summary combining both descriptions
            const aiSummary = `
                <div class="character-section">
                    <h3>🎭 ${characterName}</h3>
                    ${this.characterDescription}
                </div>
                <div class="world-section">
                    <h3>🌍 ${this.currentLanguage === 'nl' ? 'Jouw Wereld' : 'Your World'}</h3>
                    ${this.worldDescription}
                </div>
            `;
            
            const updateData = {
                character_description: this.characterDescription,
                character_name: characterName,
                ai_summary: aiSummary,
                props: '', // Empty for now, can be used later
                status: 'descriptions_approved',
                updated_at: new Date().toISOString()
            };
            
            console.log('💾 Saving to PocketBase:', {
                character_name: characterName,
                character_desc_length: this.characterDescription.length,
                world_desc_length: this.worldDescription.length,
                ai_summary_length: aiSummary.length
            });
            
            if (this.playerRecordId) {
                await pb.collection('MEQuestions').update(this.playerRecordId, updateData);
                console.log('✅ Descriptions saved to PocketBase');
            } else {
                console.warn('⚠️ No player record ID found');
            }
            
        } catch (error) {
            console.error('❌ Error saving to PocketBase:', error);
            throw error;
        }
    }

    extractCharacterName(characterDescription) {
        // Try to extract character name from the description
        // Look for patterns like:
        // - "Meet 'The Majestic Lion'"
        // - "'The Phoenix Alchemist'"
        // - "Stepping in from the shadows, 'The Majestic Scribe'"
        // - "🎭 The Silent Wolf"
        
        console.log('🔍 Extracting character name from:', characterDescription.substring(0, 150));
        
        // Pattern 1: ANY single-quoted text (most reliable)
        const pattern1 = /'([^']+)'/;
        const match1 = characterDescription.match(pattern1);
        if (match1) {
            console.log('✅ Found quoted name:', match1[1]);
            return match1[1].trim();
        }
        
        // Pattern 2: Meet 'Name' or Ontmoet 'Naam'
        const pattern2 = /(?:Meet|Ontmoet)\s+'([^']+)'/i;
        const match2 = characterDescription.match(pattern2);
        if (match2) {
            console.log('✅ Found Meet pattern:', match2[1]);
            return match2[1].trim();
        }
        
        // Pattern 3: 🎭 Name
        const pattern3 = /🎭\s*([^\n,]+)/;
        const match3 = characterDescription.match(pattern3);
        if (match3) {
            console.log('✅ Found emoji pattern:', match3[1]);
            return match3[1].trim();
        }
        
        // Pattern 4: First line if short
        const firstLine = characterDescription.split('\n')[0];
        if (firstLine && firstLine.length < 50) {
            const name = firstLine.replace(/^(Meet|Ontmoet)\s+/i, '').replace(/['"🎭]/g, '').trim();
            console.log('✅ Using first line:', name);
            return name;
        }
        
        // Fallback: Use player name
        console.log('⚠️ No pattern matched, using fallback');
        return this.currentLanguage === 'nl' ? 
            `Het Mysterieuze Karakter van ${this.playerName}` :
            `The Mysterious Character of ${this.playerName}`;
    }

    showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (show) {
            overlay.classList.add('show');
        } else {
            overlay.classList.remove('show');
        }
    }

    showError(message) {
        alert(message); // In a real app, you'd use a proper modal or toast notification
    }

    restart() {
        // Reset all data
        this.currentChapter = 0;
        this.playerName = '';
        this.answers = {};
        this.confidentialityAccepted = false;
        this.aiSummary = '';
        this.currentLanguage = 'nl';
        
        // Reset form fields
        const playerNameLang = document.getElementById('playerNameLang');
        if (playerNameLang) playerNameLang.value = '';
        
        const playerInfoFields = document.getElementById('playerInfoFields');
        if (playerInfoFields) playerInfoFields.style.display = 'none';
        
        const letsDoThisBtn = document.getElementById('letsDoThisButton');
        if (letsDoThisBtn) letsDoThisBtn.style.display = 'none';
        
        // Reset language buttons
        document.getElementById('btnDutch').classList.remove('selected');
        document.getElementById('btnEnglish').classList.remove('selected');
        
        // Hide all pages
        document.getElementById('processingPage').style.display = 'none';
        document.getElementById('summaryPage').style.display = 'none';
        document.getElementById('chapterPage').style.display = 'none';
        document.getElementById('welcomePage').style.display = 'none';
        
        // Show language selection page
        const langPage = document.getElementById('languageSelectionPage');
        langPage.style.display = 'block';
        langPage.classList.add('active');
        
        // Hide progress indicators
        document.getElementById('stepIndicator').style.display = 'none';
        document.getElementById('progressContainer').style.display = 'none';
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Utility method to get all submissions (for testing)
    getAllSubmissions() {
        return JSON.parse(localStorage.getItem('maskedEmployeeSubmissions') || '[]');
    }

    // Utility method to clear all submissions (for testing)
    clearAllSubmissions() {
        localStorage.removeItem('maskedEmployeeSubmissions');
    }

    setupEventListeners() {
        // Language selection buttons
        const btnDutch = document.getElementById('btnDutch');
        const btnEnglish = document.getElementById('btnEnglish');
        
        if (btnDutch) {
            btnDutch.addEventListener('click', () => {
                this.selectLanguage('nl');
            });
        } else {
            console.error('❌ btnDutch element not found!');
        }
        
        if (btnEnglish) {
            btnEnglish.addEventListener('click', () => {
                this.selectLanguage('en');
            });
        } else {
            console.error('❌ btnEnglish element not found!');
        }
        
        // "Let's do this!" button
        document.getElementById('letsDoThisButton').addEventListener('click', () => {
            this.proceedToWelcome();
        });
        
        // Start button - Show confidentiality modal first
        document.getElementById('startButton').addEventListener('click', () => {
            this.showConfidentialityModal();
        });
        
        // Confidentiality checkbox
        document.getElementById('confidentialityAgree').addEventListener('change', (e) => {
            document.getElementById('modalAcceptButton').disabled = !e.target.checked;
        });
        
        // Modal accept button
        document.getElementById('modalAcceptButton').addEventListener('click', () => {
            this.confidentialityAccepted = true;
            this.hideConfidentialityModal();
            this.startGameshow();
        });

        // Chapter form submission
        document.getElementById('chapterForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleChapterSubmission();
        });

        // Previous button
        document.getElementById('prevButton').addEventListener('click', () => {
            this.goToPreviousChapter();
        });

        // Restart button
        document.getElementById('restartButton').addEventListener('click', () => {
            this.restart();
        });

        // Player name input validation (on language page)
        const playerNameLangInput = document.getElementById('playerNameLang');
        if (playerNameLangInput) {
            playerNameLangInput.addEventListener('input', (e) => {
                // Show buttons only if name is filled
                const name = e.target.value.trim();
                const btn = document.getElementById('letsDoThisButton');
                const testBtn = document.getElementById('testModeButton');
                if (name.length >= 2) {
                    btn.style.display = 'block';
                    testBtn.style.display = 'block';
                } else {
                    btn.style.display = 'none';
                    testBtn.style.display = 'none';
                }
            });
        }
        
        // Test Mode Button
        const testModeButton = document.getElementById('testModeButton');
        if (testModeButton) {
            testModeButton.addEventListener('click', () => {
                this.activateTestMode();
            });
        }
        
        // Summary page confirmation options
        document.querySelectorAll('input[name="confirmation"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                document.getElementById('finalSubmitButton').disabled = false;
            });
        });
        
        // Final submit button
        document.getElementById('finalSubmitButton').addEventListener('click', () => {
            this.handleFinalSubmission();
        });

        // Email modal submit button
        document.getElementById('sendEmailButton').addEventListener('click', () => {
            this.handleEmailSubmit();
        });

        // Email input - submit on Enter key
        document.getElementById('emailInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.handleEmailSubmit();
            }
        });

        // Character Preview page buttons
        document.getElementById('regenerateButton').addEventListener('click', () => {
            this.regenerateCharacter();
        });

        document.getElementById('acceptCharacterButton').addEventListener('click', () => {
            this.acceptCharacterAndContinue();
        });
    }

    async selectLanguage(lang) {
        console.log(`🌐 Language selected: ${lang}`);
        this.currentLanguage = lang;
        
        // Update button states
        const btnDutch = document.getElementById('btnDutch');
        const btnEnglish = document.getElementById('btnEnglish');
        
        if (btnDutch && btnEnglish) {
            btnDutch.classList.toggle('selected', lang === 'nl');
            btnEnglish.classList.toggle('selected', lang === 'en');
            console.log('✅ Button states updated');
        }
        
        // Update heading to welcome title in selected language
        const heading = document.getElementById('languagePageHeading');
        if (heading) {
            heading.textContent = this.translations[lang].welcomeTitle;
            console.log('✅ Heading updated');
        }
        
        // Show player info fields with translated labels
        const playerInfoFields = document.getElementById('playerInfoFields');
        if (playerInfoFields) {
            console.log('📋 Player info fields found, showing...');
            playerInfoFields.style.display = 'block';
            console.log('✅ Player info fields displayed:', playerInfoFields.style.display);
        } else {
            console.error('❌ playerInfoFields element not found!');
        }
        
        // Update field labels
        document.getElementById('labelGameNameLang').textContent = this.translations[lang].gameNameLabel;
        document.getElementById('labelPlayerNameLang').textContent = this.translations[lang].playerNameLabel;
        document.getElementById('playerNameLang').placeholder = this.translations[lang].playerNamePlaceholder;
        
        // Set gameshow name (now a div, not input)
        document.getElementById('gameNameLang').textContent = this.gameName || 'Loading...';
        
        // Update "Let's do this!" button text (but don't show it yet)
        const letsDoThisBtn = document.getElementById('letsDoThisButton');
        letsDoThisBtn.querySelector('.lets-do-this-text').textContent = this.translations[lang].letsDoThis;
        // Button stays hidden until name is filled
        
        // Save language to PocketBase immediately
        await this.saveLanguageSelection(lang);
    }
    
    async saveLanguageSelection(lang) {
        try {
            // We'll create/update a player record with just the language for now
            // This gets updated later with full player data
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            const credentials = 'biknu8-pyrnaB-mytvyx';
            pb.authStore.save(credentials, { admin: true });
            
            // Store in localStorage for now, will be saved to PB when player starts
            localStorage.setItem('selectedLanguage', lang);
            console.log(`✅ Language '${lang}' saved to localStorage`);
        } catch (error) {
            console.error('❌ Error saving language selection:', error);
        }
    }
    
    proceedToWelcome() {
        console.log('➡️ Proceeding to welcome page');
        
        // Validate player name
        const playerNameInput = document.getElementById('playerNameLang');
        const name = playerNameInput.value.trim();
        
        if (!name || name.length < 2) {
            alert(this.currentLanguage === 'nl' ? 
                'Vul je volledige naam in.' : 
                'Please enter your full name.');
            playerNameInput.focus();
            return;
        }
        
        // Store player name
        this.playerName = name;
        
        // Hide language selection page
        document.getElementById('languageSelectionPage').classList.remove('active');
        document.getElementById('languageSelectionPage').style.display = 'none';
        
        // Show welcome page
        const welcomePage = document.getElementById('welcomePage');
        welcomePage.classList.add('active');
        welcomePage.style.display = 'block';
        
        // Update all text on welcome page
        this.updateLanguage();
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    updateLanguage() {
        const t = this.translations[this.currentLanguage];
        
        // Helper function to safely update element
        const safeUpdate = (id, text) => {
            const el = document.getElementById(id);
            if (el) el.textContent = text;
        };
        
        const safeUpdateHTML = (id, html) => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = html;
        };
        
        // Welcome page - main title and content
        safeUpdate('welcomePageTitle', t.welcomeTitle);
        safeUpdateHTML('welcomePageSubheading', t.welcomeSubheading.replace('\n', '<br>'));
        safeUpdateHTML('welcomePageBody', 
            `${t.welcomeBody1}<br>${t.welcomeBody2}<br>${t.welcomeBody3}<br>${t.welcomeBody4}<br>${t.welcomeBody5}`);
        
        // Excitement section
        safeUpdate('excitementBoxTitle', t.excitementTitle);
        
        // Update bullets
        const bulletsContainer = document.getElementById('excitementBullets');
        if (bulletsContainer) {
            bulletsContainer.innerHTML = `
                <li>${t.bullet1.replace('\n', '<br>')}</li>
                <li>${t.bullet2}</li>
                <li>${t.bullet3.replace('\n', '<br>')}</li>
                <li>${t.bullet4.replace('\n', '<br>')}</li>
            `;
        }
        
        safeUpdate('startButton', t.startButton);
        
        // Navigation
        safeUpdate('prevButtonText', t.prevButton);
        safeUpdate('nextButtonText', t.nextButton);
        safeUpdate('stepOf', t.stepOf);
        
        // Confidentiality modal
        safeUpdate('modalConfidentialityTitle', t.confidentialityTitle);
        safeUpdate('agreementText', t.agreementText);
        safeUpdate('modalAcceptButtonText', t.modalAcceptButton);
        
        // Preview page
        safeUpdate('previewTitle', t.previewTitle);
        safeUpdate('previewSubtitle', t.previewSubtitle);
        safeUpdate('loadingPreviewText', t.loadingPreview);
        safeUpdate('regenerateButtonText', t.regenerateButton);
        safeUpdate('acceptButtonText', t.acceptButton);
        safeUpdate('previewNote', t.previewNote);
        
        // Summary page
        safeUpdate('summaryTitle', t.summaryTitle);
        safeUpdate('confirmationQuestion', t.confirmationQuestion);
        safeUpdate('redoText', t.redoText);
        safeUpdate('confirmText', t.confirmText);
        safeUpdate('finalSubmitButtonText', t.finalSubmitButton);
        safeUpdate('loadingSummaryText', t.aiAnalyzing);
        
        // Processing page
        safeUpdate('processingTitle', t.processingTitle);
        safeUpdate('processingThankYou', t.processingThankYou);
        safeUpdate('processingDescription', t.processingDescription);
        safeUpdate('processingWhatNow', t.processingWhatNow);
        safeUpdate('processingStep1', t.processingStep1);
        safeUpdate('processingStep2', t.processingStep2);
        safeUpdate('processingStep3', t.processingStep3);
        safeUpdate('processingStep4', t.processingStep4);
        safeUpdate('restartButtonText', t.restartButton);
        
        // Loading overlay
        safeUpdate('loadingText', t.loadingText);
        
        console.log(`✅ Language updated to: ${this.currentLanguage}`);
    }

    updateGameNameDisplay() {
        // Update gameNameLang on language selection page (now a div, not input)
        const gameNameLang = document.getElementById('gameNameLang');
        if (gameNameLang && this.gameName) {
            gameNameLang.textContent = this.gameName;
        }
        
        // Legacy support for old element (if it exists on welcome page)
        const gamenameElement = document.getElementById('gamename-display');
        if (gamenameElement) {
            gamenameElement.textContent = this.gameName;
            
            // Only show the element once we have dynamic data
            if (this.gamenameLoaded) {
                gamenameElement.style.opacity = '1';
                gamenameElement.style.visibility = 'visible';
            }
        }
    }

    displayWelcomePage() {
        console.log('📋 displayWelcomePage called');
        
        // Ensure we have gameshow data
        if (!this.questionsData || !this.questionsData.gameshow) {
            console.error('❌ No gameshow data available!');
            console.log('🔄 Using fallback content...');
            
            // Fallback content if JSON fails to load
            const welcomeTitle = document.getElementById('welcomeTitle');
            welcomeTitle.textContent = 'ONTDEK DE MYSTERIEUZE HELD IN JEZELF!';
            console.log('✅ Fallback title set');
            return;
        }
        
        const gameshow = this.questionsData.gameshow;
        const lang = this.currentLanguage;
        console.log('📊 Gameshow data:', gameshow);
        console.log('🌍 Current language:', lang);

        // Set welcome content with language support
        const welcomeTitle = document.getElementById('welcomeTitle');
        welcomeTitle.textContent = gameshow.welcome_title[lang] || gameshow.welcome_title;
        console.log('✅ Welcome title set to:', gameshow.welcome_title[lang]);
        
        // Handle congratulations with proper HTML structure
        const congratulationsElement = document.getElementById('congratulationsText');
        congratulationsElement.innerHTML = `
            <p>Ben jij klaar voor het avontuur van je leven? The Masked Employee gaat beginnen en <strong>WIJ ZOEKEN JOUW VERBORGEN SUPERKRACHT!</strong></p>
            
            <p>Dit is geen gewone vragenlijst - dit is jouw kans om te schitteren als de mysterieuze held die je altijd al was. Vergeet alles wat je denkt over "normaal zijn" op kantoor. Het is tijd om die gekke, creatieve, onverwachte kant van jezelf te laten zien die thuis misschien operazanger is, pizza met ananas eet, of stiekem alle K-pop choreografieën uit het hoofd kent!</p>
            
            <div class="excitement-section">
                <h3>🚀 DEZE VRAGENLIJST = JOUW TICKET NAAR ROEM!</h3>
                <ul>
                    <li>Geef antwoorden die je collega's hun koffie laten verslikken van verbazing</li>
                    <li>Denk buiten alle hokjes - hoe gekker, hoe beter!</li>
                    <li>Dit is JOUW moment om te laten zien wie je werkelijk bent</li>
                    <li>Maak er een feestje van - geniet van elke vraag!</li>
                </ul>
            </div>
        `;
        
        document.getElementById('confidentialityTitle').textContent = gameshow.confidentiality_title[lang] || gameshow.confidentiality_title;
        document.getElementById('confidentialityWarning').textContent = gameshow.confidentiality_warning[lang] || gameshow.confidentiality_warning;
        document.getElementById('penaltyClause').textContent = gameshow.penalty_clause[lang] || gameshow.penalty_clause;
        document.getElementById('howItWorksTitle').textContent = gameshow.how_it_works_title[lang] || gameshow.how_it_works_title;
        document.getElementById('finalMessage').textContent = gameshow.final_message[lang] || gameshow.final_message;

        // Set forbidden rules with language support
        const forbiddenList = document.getElementById('forbiddenRules');
        const forbiddenRules = gameshow.forbidden_rules[lang] || gameshow.forbidden_rules;
        forbiddenList.innerHTML = '';
        forbiddenRules.forEach(rule => {
            const li = document.createElement('li');
            li.textContent = rule;
            forbiddenList.appendChild(li);
        });

        // Set how it works list with language support
        const howItWorksList = document.getElementById('howItWorksList');
        const howItWorks = gameshow.how_it_works[lang] || gameshow.how_it_works;
        howItWorksList.innerHTML = '';
        howItWorks.forEach(step => {
            const li = document.createElement('li');
            li.textContent = step;
            howItWorksList.appendChild(li);
        });

        // Set total steps
        document.getElementById('totalSteps').textContent = this.totalChapters;
        
        // Update gamename display
        this.updateGameNameDisplay();
    }

    validatePlayerName(name) {
        // This function is no longer used since validation happens on language page
        return name.trim().length >= 2;
    }

    async startGameshow() {
        // Player name is already set from language selection page
        if (!this.playerName || this.playerName.trim().length < 2) {
            this.showError(this.currentLanguage === 'nl' ? 
                'Je naam is niet ingevuld.' :
                'Your name is not filled in.');
            return;
        }

        // Gamename is already loaded from PocketBase and is readonly
        if (!this.gameName) {
            this.showError(this.currentLanguage === 'nl' ? 
                'Gameshow gegevens zijn nog niet geladen. Wacht even en probeer opnieuw.' :
                'Game show data is not loaded yet. Please wait and try again.');
            return;
        }
        
        if (!this.confidentialityAccepted) {
            this.showError(this.currentLanguage === 'nl' ? 
                'Je moet eerst akkoord gaan met de geheimhoudingsverklaring.' :
                'You must first agree to the confidentiality agreement.');
            return;
        }
        this.updateGameNameDisplay();
        this.currentChapter = 1;
        this.answers = {};
        
        // Show loading while creating initial PocketBase record
        this.showLoading(true);
        
        try {
            // Save initial player data to PocketBase immediately
            await this.saveInitialPlayerData();
            console.log('✅ Initial player data saved to PocketBase');
            
            // Hide welcome page and show first chapter
            document.getElementById('welcomePage').style.display = 'none';
            document.getElementById('chapterPage').style.display = 'block';
            
            // Show progress indicators
            document.getElementById('stepIndicator').style.display = 'block';
            document.getElementById('progressContainer').style.display = 'flex';
            
            this.displayChapter(this.currentChapter);
            
        } catch (error) {
            console.error('❌ Error saving initial player data:', error);
            // Show error but allow user to continue
            const continueAnyway = confirm(this.currentLanguage === 'nl' ?
                'Waarschuwing: Je gegevens konden niet worden opgeslagen in de database. Wil je toch doorgaan? (Je antwoorden worden lokaal opgeslagen als backup.)' :
                'Warning: Your data could not be saved to the database. Do you want to continue anyway? (Your answers will be saved locally as backup.)');
            
            if (continueAnyway) {
                // Continue with form even if PB save failed
                document.getElementById('welcomePage').style.display = 'none';
                document.getElementById('chapterPage').style.display = 'block';
                document.getElementById('stepIndicator').style.display = 'block';
                document.getElementById('progressBar').style.display = 'block';
                this.displayChapter(this.currentChapter);
            }
        } finally {
            this.showLoading(false);
        }
    }

    displayChapter(chapterNumber) {
        const chapter = this.questionsData.chapters[chapterNumber - 1];
        if (!chapter) {
            console.error('Chapter not found:', chapterNumber);
            return;
        }

        const lang = this.currentLanguage;

        // Update header
        document.getElementById('currentStep').textContent = chapterNumber;
        const progressPercentage = Math.round((chapterNumber / this.totalChapters) * 100);
        document.getElementById('progressFill').style.width = `${progressPercentage}%`;
        document.getElementById('progressPercentage').textContent = `${progressPercentage}%`;

        // Update chapter info with language support
        document.getElementById('chapterTitle').textContent = chapter.title[lang] || chapter.title;
        document.getElementById('chapterDescription').textContent = chapter.description[lang] || chapter.description;

        // Clear previous questions
        const questionsContainer = document.getElementById('questionsContainer');
        questionsContainer.innerHTML = '';

        // Add questions
        chapter.questions.forEach(question => {
            const questionDiv = this.createQuestionElement(question);
            questionsContainer.appendChild(questionDiv);
        });

        // Update navigation buttons
        const prevButton = document.getElementById('prevButton');
        const nextButton = document.getElementById('nextButton');
        
        prevButton.style.display = chapterNumber > 1 ? 'block' : 'none';
        nextButton.textContent = chapterNumber === this.totalChapters ? '🎭 Voltooien!' : 'Volgende →';

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    createQuestionElement(question) {
        const lang = this.currentLanguage;
        const questionDiv = document.createElement('div');
        questionDiv.className = 'question';
        questionDiv.dataset.questionId = question.id;

        const questionText = question.question[lang] || question.question;
        const questionTitle = document.createElement('h3');
        questionTitle.innerHTML = `
            <span class="question-number">${question.id}</span>
            ${questionText}
        `;

        questionDiv.appendChild(questionTitle);

        if (question.type === 'multiple-choice') {
            const optionsList = document.createElement('ul');
            optionsList.className = 'options';

            const options = question.options[lang] || question.options;
            options.forEach((option, index) => {
                const li = document.createElement('li');
                const label = document.createElement('label');
                
                const radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = `question_${question.id}`;
                radio.value = index;
                radio.required = true;

                // Restore previous answer if exists
                if (this.answers[question.id] !== undefined && this.answers[question.id] == index) {
                    radio.checked = true;
                }

                const span = document.createElement('span');
                span.textContent = option;

                label.appendChild(radio);
                label.appendChild(span);
                li.appendChild(label);
                optionsList.appendChild(li);
            });

            questionDiv.appendChild(optionsList);
        } else if (question.type === 'text') {
            // Check if this question has scenes (Chapter 9 questions)
            if (question.scenes && Array.isArray(question.scenes[lang])) {
                const scenesContainer = document.createElement('div');
                scenesContainer.className = 'scenes-container';
                
                question.scenes[lang].forEach((sceneText, index) => {
                    const sceneDiv = document.createElement('div');
                    sceneDiv.className = 'scene-input-group';
                    
                    const sceneLabel = document.createElement('label');
                    sceneLabel.className = 'scene-label';
                    sceneLabel.textContent = sceneText;
                    
                    const textarea = document.createElement('textarea');
                    textarea.className = 'text-input scene-textarea';
                    textarea.name = `question_${question.id}_scene${index + 1}`;
                    textarea.placeholder = lang === 'nl' ? 
                        `Beschrijf scene ${index + 1}...` : 
                        `Describe scene ${index + 1}...`;
                    textarea.required = true;
                    textarea.rows = 3;
                    
                    // Restore previous answer if exists
                    const answerKey = `${question.id}_scene${index + 1}`;
                    if (this.answers[answerKey] !== undefined) {
                        textarea.value = this.answers[answerKey];
                    }
                    
                    sceneDiv.appendChild(sceneLabel);
                    sceneDiv.appendChild(textarea);
                    scenesContainer.appendChild(sceneDiv);
                });
                
                questionDiv.appendChild(scenesContainer);
            } else {
                // Regular text question (no scenes)
                const placeholder = question.placeholder ? (question.placeholder[lang] || question.placeholder) : 
                    (lang === 'nl' ? 'Vul je antwoord hier in...' : 'Enter your answer here...');
                
                const textarea = document.createElement('textarea');
                textarea.className = 'text-input';
                textarea.name = `question_${question.id}`;
                textarea.placeholder = placeholder;
                textarea.required = true;

                // Restore previous answer if exists
                if (this.answers[question.id] !== undefined) {
                    textarea.value = this.answers[question.id];
                }

                questionDiv.appendChild(textarea);
            }
        }

        // Add error message container
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = lang === 'nl' ? 'Dit veld is verplicht.' : 'This field is required.';
        questionDiv.appendChild(errorDiv);

        return questionDiv;
    }

    async handleChapterSubmission() {
        const chapter = this.questionsData.chapters[this.currentChapter - 1];
        const isValid = this.validateChapter(chapter);

        if (!isValid) {
            return;
        }

        // Save answers for current chapter
        this.saveChapterAnswers(chapter);
        
        // Show loading while saving to PocketBase
        this.showLoading(true);
        
        try {
            // Save current progress to PocketBase after each chapter
            await this.saveProgressToPocketBase();
            console.log(`✅ Chapter ${this.currentChapter} saved to PocketBase successfully`);
            
            if (this.currentChapter === this.totalChapters) {
                // Final submission - generate character and show completion
                await this.submitAllAnswers();
            } else {
                // Go to next chapter
                this.currentChapter++;
                this.displayChapter(this.currentChapter);
            }
        } catch (error) {
            console.error('❌ Error saving chapter to PocketBase:', error);
            // Show error but allow user to continue
            alert(`Waarschuwing: Je antwoorden voor hoofdstuk ${this.currentChapter} konden niet worden opgeslagen. Probeer het opnieuw of neem contact op met de organisator.`);
        } finally {
            this.showLoading(false);
        }
    }

    validateChapter(chapter) {
        let isValid = true;
        const questionsContainer = document.getElementById('questionsContainer');

        chapter.questions.forEach(question => {
            const questionDiv = questionsContainer.querySelector(`[data-question-id="${question.id}"]`);
            const errorDiv = questionDiv.querySelector('.error-message');
            
            // Remove previous error state
            questionDiv.classList.remove('error');
            errorDiv.classList.remove('show');

            let hasAnswer = false;

            if (question.type === 'multiple-choice') {
                const radios = questionDiv.querySelectorAll('input[type="radio"]');
                hasAnswer = Array.from(radios).some(radio => radio.checked);
            } else if (question.type === 'text') {
                const textarea = questionDiv.querySelector('textarea');
                hasAnswer = textarea.value.trim().length > 0;
            }

            if (!hasAnswer) {
                questionDiv.classList.add('error');
                errorDiv.classList.add('show');
                isValid = false;
            }
        });

        if (!isValid) {
            // Scroll to first error
            const firstError = questionsContainer.querySelector('.question.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        return isValid;
    }

    saveChapterAnswers(chapter) {
        const questionsContainer = document.getElementById('questionsContainer');

        chapter.questions.forEach(question => {
            const questionDiv = questionsContainer.querySelector(`[data-question-id="${question.id}"]`);

            if (question.type === 'multiple-choice') {
                const checkedRadio = questionDiv.querySelector('input[type="radio"]:checked');
                if (checkedRadio) {
                    this.answers[question.id] = parseInt(checkedRadio.value);
                    
                    // Special handling for Question 7 (Character Type Selection)
                    if (question.id === 7 && question.characterTypeMapping) {
                        const selectedOption = question.options[this.currentLanguage][parseInt(checkedRadio.value)];
                        this.characterType = question.characterTypeMapping[selectedOption];
                        console.log('✅ Character type selected:', this.characterType);
                    }
                }
            } else if (question.type === 'text') {
                // Check if this question has scenes
                const lang = this.currentLanguage;
                if (question.scenes && Array.isArray(question.scenes[lang])) {
                    // Save each scene separately
                    const textareas = questionDiv.querySelectorAll('textarea');
                    textareas.forEach((textarea, index) => {
                        const answerKey = `${question.id}_scene${index + 1}`;
                        this.answers[answerKey] = textarea.value.trim();
                    });
                } else {
                    // Regular text question
                    const textarea = questionDiv.querySelector('textarea');
                    this.answers[question.id] = textarea.value.trim();
                    
                    // Special handling for Question 6 (Department)
                    if (question.id === 6) {
                        this.department = textarea.value.trim();
                        console.log('✅ Department saved:', this.department);
                    }
                }
            }
        });
    }

    goToPreviousChapter() {
        if (this.currentChapter > 1) {
            this.currentChapter--;
            this.displayChapter(this.currentChapter);
        }
    }

    async submitAllAnswers() {
        this.showLoading(true);

        try {
            const submissionData = {
                timestamp: new Date().toISOString(),
                playerName: this.playerName,
                answers: this.answers,
                totalQuestions: Object.keys(this.answers).length
            };

            console.log('📤 Step 1: Generating character data...');
            // Generate character data using OpenAI
            const characterData = await this.generateCharacterData(submissionData);
            console.log('✅ Character data generated:', characterData);

            // Store character data for later use (email, image generation)
            this.currentCharacterData = characterData;

            console.log('📤 Step 2: Saving to PocketBase...');
            // Save to PocketBase with character data
            await this.saveToPocketBase(submissionData, characterData);
            console.log('✅ Saved to PocketBase successfully');
            
            // Show completion page with character data
            this.showCompletionPage(characterData);
            
            // Note: Image generation will start after user provides email
            // See acceptCharacterAndContinue() -> email modal -> generateAndUploadImage()
        } catch (error) {
            console.error('❌ Submission error:', error);
            this.showError('Er is een fout opgetreden bij het opslaan van je antwoorden. Probeer het opnieuw.');
        } finally {
            this.showLoading(false);
        }
    }

    async generateAndUploadImage(characterData) {
        try {
            console.log('🎨 Step 1: Generating image via Leonardo.ai...');
            
            const imagePrompt = characterData.image_generation_prompt;
            if (!imagePrompt) {
                throw new Error('No image prompt available');
            }
            
            // Store character data for email
            this.characterName = characterData.character_name || 'Your Character';
            this.characterDescription = characterData.ai_summary || '';
            this.imagePrompt = imagePrompt;
            
            // Call Leonardo.ai API with timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 90000); // 90 second timeout
            
            const requestBody = {
                playerName: this.playerName,
                prompt: imagePrompt
            };
            
            console.log('📤 Calling Leonardo.ai API...');
            console.log('📝 Request body:', {
                playerName: requestBody.playerName,
                promptLength: requestBody.prompt.length,
                promptPreview: requestBody.prompt.substring(0, 100) + '...'
            });
            
            const response = await fetch('https://www.pinkmilk.eu/ME/generate-image-leonardo.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestBody),
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            console.log('📥 Response received, status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Leonardo.ai API error response:', errorText);
                throw new Error(`Image generation failed: ${response.status} - ${errorText.substring(0, 200)}`);
            }
            
            console.log('📋 Parsing JSON response...');
            const responseText = await response.text();
            console.log('📄 Raw response (first 500 chars):', responseText.substring(0, 500));
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('📊 Result:', result);
            } catch (e) {
                console.error('❌ Failed to parse JSON response');
                console.error('Full response:', responseText);
                throw new Error('Invalid JSON response from Leonardo.ai API: ' + responseText.substring(0, 200));
            }
            
            if (!result.success) {
                throw new Error(result.error || 'Image generation failed');
            }
            
            console.log('✅ Image generated successfully');
            
            // Step 2: Upload to PocketBase
            const imageData = result.image_data || result.image_binary;
            if (imageData) {
                const imageBlob = this.base64ToBlob(imageData, 'image/png');
                await this.uploadImageToPocketBase(imageBlob);
                console.log('✅ Image uploaded to PocketBase');
            }
            
            // Step 3: Send email with image
            await this.sendFinalEmailWithImage();
            console.log('✅ Email sent with image');
            
        } catch (error) {
            if (error.name === 'AbortError') {
                console.error('❌ Image generation timeout (90 seconds)');
                throw new Error('Image generation timed out - Leonardo.ai API took too long');
            }
            console.error('❌ Error in image generation:', error);
            console.error('Error details:', error.message);
            throw error;
        }
    }

    async generateCharacterData(submissionData) {
        try {
            console.log('🤖 Calling generate-character.php...');
            console.log('📋 Character Type:', this.characterType);
            console.log('🏢 Department:', this.department);
            
            const response = await fetch('generate-character.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    playerName: submissionData.playerName,
                    answers: submissionData.answers,
                    gameName: this.gameName,
                    characterType: this.characterType, // User-selected character type
                    department: this.department // User's department
                })
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Character generation failed:', errorText);
                throw new Error(`Character generation failed: ${response.status}`);
            }

            const characterData = await response.json();
            
            if (!characterData.success) {
                throw new Error(characterData.error || 'Character generation failed');
            }

            console.log('✅ Character generated:', characterData.character_name);
            return characterData;
        } catch (error) {
            console.error('❌ Error generating character:', error);
            // Return empty character data if generation fails (so form doesn't break)
            return {
                success: false,
                character_name: '',
                character_type: '',
                personality_traits: '',
                ai_summary: '',
                story_prompt_level1: '',
                story_prompt_level2: '',
                story_prompt_level3: '',
                image_generation_prompt: '',
                error: error.message
            };
        }
    }

    // ============================================
    // TEST MODE
    // ============================================

    async activateTestMode() {
        console.log('🧪 TEST MODE ACTIVATED - Loading last PocketBase record');
        
        try {
            // Connect to PocketBase
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            const credentials = 'biknu8-pyrnaB-mytvyx';
            pb.authStore.save(credentials, { admin: true });
            
            console.log('🔌 Connected to PocketBase');
            
            // Get the last record from MEQuestions collection
            const records = await pb.collection('MEQuestions').getList(1, 1, {
                sort: '-created',
                filter: `gamename = "${this.gameName}"`
            });
            
            if (!records.items || records.items.length === 0) {
                throw new Error('No records found in PocketBase');
            }
            
            const lastRecord = records.items[0];
            console.log('✅ Last record loaded:', lastRecord.nameplayer);
            
            // Extract answers from all chapters
            this.answers = {};
            
            // Combine all chapter answers
            for (let i = 1; i <= 9; i++) {
                const chapterKey = `chapter${String(i).padStart(2, '0')}`;
                const chapterData = lastRecord[chapterKey];
                
                if (chapterData && typeof chapterData === 'object') {
                    Object.assign(this.answers, chapterData);
                }
            }
            
            console.log('✅ Loaded answers:', Object.keys(this.answers).length, 'questions');
            
            // Set player name from record
            this.playerName = lastRecord.nameplayer || 'Test User';
            
            // Hide language page
            document.getElementById('languageSelectionPage').style.display = 'none';
            
            // Jump to last chapter (chapter 9)
            this.currentChapter = this.totalChapters;
            this.displayChapter(this.currentChapter);
            
            // Show chapter page
            document.getElementById('chapterPage').style.display = 'block';
            
            console.log('🎉 Test mode complete - jumped to last chapter');
            console.log('💡 Use Previous button to go back and edit answers');
            console.log('💡 Click Voltooien to regenerate character');
            
        } catch (error) {
            console.error('❌ Error in test mode:', error);
            alert('Error loading last record from PocketBase:\n' + error.message);
        }
    }
}

// Initialize the form when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.maskedEmployeeForm = new MaskedEmployeeForm();
});

// Console utility functions
window.debugForm = {
    getAnswers: () => window.maskedEmployeeForm?.answers || {},
    activateTest: () => window.maskedEmployeeForm?.activateTestMode(),
    reset: () => location.reload()
};

// ===== ANIMATED BACKGROUND =====
const colorSchemes = {
    blue: ['#001F3F', '#003366', '#004C99', '#0066CC', '#0080FF', '#3399FF'],
    green: ['#004D1A', '#006622', '#008033', '#009933', '#00B33C', '#00CC44'],
    purple: ['#2A0033', '#3F004D', '#530066', '#660080', '#7A0099', '#8F00B3'],
    red: ['#330000', '#660000', '#990000', '#CC0000', '#FF0000', '#FF3333'],
    teal: ['#003333', '#004D4D', '#006666', '#008080', '#009999', '#00B3B3'],
    orange: ['#331100', '#662200', '#993300', '#CC4400', '#FF5500', '#FF7733'],
    pink: ['#330033', '#4D004D', '#660066', '#800080', '#990099', '#B300B3'],
    yellow: ['#332600', '#664D00', '#997300', '#CC9900', '#FFBF00', '#FFD633']
};

const columns = Object.keys(colorSchemes);

function createColumns() {
    const wrapper = document.getElementById('wrapper');
    if (!wrapper) return;
    
    wrapper.innerHTML = '';
    
    columns.forEach((columnColor, index) => {
        const column = document.createElement('div');
        column.className = 'column';
        column.dataset.colorScheme = columnColor;
        
        // Create boxes for each column
        const boxCount = Math.ceil(window.innerHeight / 16); // 1rem = 16px
        for (let i = 0; i < boxCount; i++) {
            const box = document.createElement('div');
            box.className = 'box';
            column.appendChild(box);
        }
        
        wrapper.appendChild(column);
    });
}

function animateColumn(column, direction = 1) {
    const boxes = Array.from(column.querySelectorAll('.box'));
    const colorScheme = colorSchemes[column.dataset.colorScheme];
    
    // Get current colors
    const currentColors = boxes.map(box => {
        const color = box.style.backgroundColor;
        return colorScheme.indexOf(rgbToHex(color)) !== -1 ? 
            rgbToHex(color) : 
            colorScheme[0];
    });

    // Shift colors
    if (direction > 0) {
        const lastColor = currentColors.pop();
        currentColors.unshift(lastColor);
    } else {
        const firstColor = currentColors.shift();
        currentColors.push(firstColor);
    }

    // Apply new colors
    boxes.forEach((box, i) => {
        box.style.backgroundColor = currentColors[i] || colorScheme[0];
    });
}

function initializeColumns() {
    const columns = document.querySelectorAll('.column');
    columns.forEach(column => {
        const boxes = column.querySelectorAll('.box');
        const colorScheme = colorSchemes[column.dataset.colorScheme];
        
        boxes.forEach((box, index) => {
            const colorIndex = index % colorScheme.length;
            box.style.backgroundColor = colorScheme[colorIndex];
        });
    });
}

// Helper function to convert RGB to Hex
function rgbToHex(rgb) {
    if (!rgb) return '#000000';
    if (rgb.startsWith('#')) return rgb;
    
    const rgbMatch = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if (!rgbMatch) return '#000000';
    
    const r = parseInt(rgbMatch[1]);
    const g = parseInt(rgbMatch[2]);
    const b = parseInt(rgbMatch[3]);
    
    return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
}

// Initialize animated background when DOM is loaded
function initAnimatedBackground() {
    createColumns();
    initializeColumns();

    // Animate each column independently
    document.querySelectorAll('.column').forEach((column, index) => {
        setInterval(() => {
            // Alternate direction based on column index
            const direction = index % 2 === 0 ? 1 : -1;
            animateColumn(column, direction);
        }, 200);
    });
}

// Recreate grid on window resize
window.addEventListener('resize', () => {
    createColumns();
    initializeColumns();
});

// Initialize animated background when page loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAnimatedBackground);
} else {
    initAnimatedBackground();
}
