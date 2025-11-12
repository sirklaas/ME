class MaskedEmployeeForm {
    constructor() {
        this.questionsData = null;
        this.currentChapter = 0;
        this.playerName = '';
        this.answers = {};
        this.totalChapters = 0;
        this.gameName = '';
        this.gamenameLoaded = false; // Track if dynamic gamename is loaded
        
        this.init();
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
            
            console.log('📋 Step 5: Displaying welcome page...');
            this.displayWelcomePage();
            console.log('✅ Welcome page displayed');
            
            console.log('🎉 FORM INITIALIZATION COMPLETE!');
        } catch (error) {
            console.error('💥 FAILED TO INITIALIZE FORM:', error);
            console.error('Error details:', error.stack);
            this.showError('Er is een fout opgetreden bij het laden van de vragenlijst.');
        }
    }

    async loadConfig() {
        try {
            // Load main gameshow configuration with STRONG cache busting
            const timestamp = new Date().getTime();
            const random = Math.random().toString(36).substring(7);
            const configResponse = await fetch(`gameshow-config-v2.json?v=${timestamp}&r=${random}&nocache=1`, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });
            console.log('🔄 Loading config with cache busting:', `gameshow-config-v2.json?v=${timestamp}&r=${random}&nocache=1`);
            if (!configResponse.ok) {
                throw new Error(`HTTP error! status: ${configResponse.status}`);
            }
            const config = await configResponse.json();
            console.log('📊 Raw config loaded:', config);
            
            // Store the complete config structure
            this.questionsData = {
                gameshow: config.gameshow,
                chapters: config.chapters
            };
        } catch (error) {
            console.error('Error loading config:', error);
            throw error;
        }
    }

    async loadAllChapters() {
        try {
            // Load individual chapter files
            const chapterData = [];
            for (const chapterFile of this.questionsData.chapters) {
                const chapterResponse = await fetch(chapterFile);
                if (!chapterResponse.ok) {
                    throw new Error(`HTTP error loading ${chapterFile}! status: ${chapterResponse.status}`);
                }
                const chapterContent = await chapterResponse.json();
                chapterData.push(chapterContent);
            }
            
            // Replace the file names with the actual chapter data
            this.questionsData.chapters = chapterData;
            
            this.totalChapters = chapterData.length;
            console.log(`Loaded ${this.totalChapters} chapters successfully`);
        } catch (error) {
            console.error('Error loading chapters:', error);
            throw error;
        }
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
            console.log('📝 Please check PocketBase admin panel permissions');
            
            // Show error in the banner so you know it's not working
            this.gameName = '🚨 PocketBase Connection Failed - Check Permissions';
            this.updateGameNameDisplay();
            
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

    async saveToPocketBase(data) {
        // Organize answers by chapter to match PocketBase structure
        const chapterAnswers = this.organizeAnswersByChapter(data.answers);
        
        // Prepare data for PocketBase schema with separate chapter fields
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
            submission_date: data.timestamp,
            total_questions: data.totalQuestions,
            status: 'completed'
        };
        
        try {
            // Simulate API delay
            await new Promise(resolve => setTimeout(resolve, 1500));
            
            // Save to localStorage as backup
            const submissions = JSON.parse(localStorage.getItem('maskedEmployeeSubmissions') || '[]');
            submissions.push(submissionData);
            localStorage.setItem('maskedEmployeeSubmissions', JSON.stringify(submissions));
            
            console.log('Submission saved:', submissionData);
            
            // PocketBase integration
            const pb = new PocketBase('https://pinkmilk.pockethost.io');
            const record = await pb.collection('submissions').create(submissionData);
            console.log('Submission saved to PocketBase:', record);
            return record;
        } catch (error) {
            console.error('PocketBase save error:', error);
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
            chapter08: [37, 38, 39, 40]           // Onverwachte Voorkeuren
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

    showCompletionPage() {
        document.getElementById('chapterPage').style.display = 'none';
        document.getElementById('completionPage').style.display = 'block';
        document.getElementById('completionPlayerName').textContent = this.playerName;
        
        // Hide progress indicators
        document.getElementById('stepIndicator').style.display = 'none';
        document.getElementById('progressBar').style.display = 'none';
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
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
        
        // Reset form
        document.getElementById('playerName').value = '';
        document.getElementById('startButton').disabled = false;
        
        // Show welcome page
        document.getElementById('completionPage').style.display = 'none';
        document.getElementById('chapterPage').style.display = 'none';
        document.getElementById('welcomePage').style.display = 'block';
        
        // Hide progress indicators
        document.getElementById('stepIndicator').style.display = 'none';
        document.getElementById('progressBar').style.display = 'none';
        
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
        // Start button
        document.getElementById('startButton').addEventListener('click', () => {
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

        // Player name input validation
        document.getElementById('playerName').addEventListener('input', (e) => {
            this.validatePlayerName(e.target.value);
        });

        // Game name input handling
        document.getElementById('gameName').addEventListener('input', (e) => {
            this.gameName = e.target.value;
            this.updateGameNameDisplay();
        });
    }

    updateGameNameDisplay() {
        const gamenameElement = document.getElementById('gamename-display');
        if (gamenameElement) {
            gamenameElement.textContent = this.gameName;
            
            // Only show the element once we have dynamic data
            if (this.gamenameLoaded) {
                gamenameElement.style.opacity = '1';
                gamenameElement.style.visibility = 'visible';
            }
        }
        
        // Also update the input field with the dynamic gamename
        const gamenameInput = document.getElementById('gameName');
        if (gamenameInput && this.gamenameLoaded && this.gameName) {
            gamenameInput.value = this.gameName;
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
        console.log('📊 Gameshow data:', gameshow);

        // Set welcome content
        const welcomeTitle = document.getElementById('welcomeTitle');
        welcomeTitle.textContent = gameshow.welcome_title;
        console.log('✅ Welcome title set to:', gameshow.welcome_title);
        
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
        
        document.getElementById('confidentialityTitle').textContent = gameshow.confidentiality_title;
        document.getElementById('confidentialityWarning').textContent = gameshow.confidentiality_warning;
        document.getElementById('penaltyClause').textContent = gameshow.penalty_clause;
        document.getElementById('howItWorksTitle').textContent = gameshow.how_it_works_title;
        document.getElementById('finalMessage').textContent = gameshow.final_message;

        // Set forbidden rules
        const forbiddenList = document.getElementById('forbiddenRules');
        forbiddenList.innerHTML = '';
        gameshow.forbidden_rules.forEach(rule => {
            const li = document.createElement('li');
            li.textContent = rule;
            forbiddenList.appendChild(li);
        });

        // Set how it works list
        const howItWorksList = document.getElementById('howItWorksList');
        howItWorksList.innerHTML = '';
        gameshow.how_it_works.forEach(step => {
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
        const startButton = document.getElementById('startButton');
        const isValid = name.trim().length >= 2;
        
        startButton.disabled = !isValid;
        return isValid;
    }

    async startGameshow() {
        const playerNameInput = document.getElementById('playerName');
        const name = playerNameInput.value.trim();
        
        if (!this.validatePlayerName(name)) {
            this.showError('Vul je volledige naam in (minimaal 2 karakters).');
            playerNameInput.focus();
            return;
        }

        // Gamename is already loaded from PocketBase and is readonly
        if (!this.gameName) {
            this.showError('Gameshow gegevens zijn nog niet geladen. Wacht even en probeer opnieuw.');
            return;
        }

        this.playerName = name;
        // this.gameName is already set from PocketBase loadGamename()
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
            document.getElementById('progressBar').style.display = 'block';
            
            this.displayChapter(this.currentChapter);
            
        } catch (error) {
            console.error('❌ Error saving initial player data:', error);
            // Show error but allow user to continue
            const continueAnyway = confirm('Waarschuwing: Je gegevens konden niet worden opgeslagen in de database. Wil je toch doorgaan? (Je antwoorden worden lokaal opgeslagen als backup.)');
            
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

        // Update header
        document.getElementById('currentStep').textContent = chapterNumber;
        document.getElementById('progressFill').style.width = `${(chapterNumber / this.totalChapters) * 100}%`;

        // Update chapter info
        document.getElementById('chapterTitle').textContent = chapter.title;
        document.getElementById('chapterDescription').textContent = chapter.description;

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
        const questionDiv = document.createElement('div');
        questionDiv.className = 'question';
        questionDiv.dataset.questionId = question.id;

        const questionTitle = document.createElement('h3');
        questionTitle.innerHTML = `
            <span class="question-number">${question.id}</span>
            ${question.question}
        `;

        questionDiv.appendChild(questionTitle);

        if (question.type === 'multiple-choice') {
            const optionsList = document.createElement('ul');
            optionsList.className = 'options';

            question.options.forEach((option, index) => {
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
            const textarea = document.createElement('textarea');
            textarea.className = 'text-input';
            textarea.name = `question_${question.id}`;
            textarea.placeholder = question.placeholder || 'Vul je antwoord hier in...';
            textarea.required = true;

            // Restore previous answer if exists
            if (this.answers[question.id] !== undefined) {
                textarea.value = this.answers[question.id];
            }

            questionDiv.appendChild(textarea);
        }

        // Add error message container
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = 'Dit veld is verplicht.';
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
                // Final submission - show completion page
                this.showCompletionPage();
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
                }
            } else if (question.type === 'text') {
                const textarea = questionDiv.querySelector('textarea');
                this.answers[question.id] = textarea.value.trim();
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

            // Save to PocketBase
            await this.saveToPocketBase(submissionData);
            
            // Show completion page
            this.showCompletionPage();
        } catch (error) {
            console.error('Submission error:', error);
            this.showError('Er is een fout opgetreden bij het opslaan van je antwoorden. Probeer het opnieuw.');
        } finally {
            this.showLoading(false);
        }
    }
}

// Initialize the form when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.maskedEmployeeForm = new MaskedEmployeeForm();
});

// Add some utility functions for debugging
window.debugForm = {
    getAnswers: () => window.maskedEmployeeForm?.answers || {},
    getSubmissions: () => window.maskedEmployeeForm?.getAllSubmissions() || [],
    clearSubmissions: () => window.maskedEmployeeForm?.clearAllSubmissions(),
    getCurrentChapter: () => window.maskedEmployeeForm?.currentChapter || 0
};
