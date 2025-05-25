<?php function drawHirings() { ?>
    <?php 
        $session = Session::getInstance();
        $user = $session->getUser();
        if (!$user) {
            return;
        }
        $userId = $user->id;
    ?>
    <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($session->getCSRFToken()) ?>">

    <div id="hirings-container">
        <button id="hirings-toggle-btn">
            <i class="fa-solid fa-briefcase"></i>
            <span id="hirings-badge" class="hirings-badge hidden"></span>
        </button>

        <div id="hirings-modal" class="hidden">
            <div class="hirings-wrapper">                
                <?php
                    drawReceivedHirings($userId);
                    
                    drawMainHiring();
                    
                    drawOwnHirings($userId);

                    drawHiringsCloseButton();
                ?> 
            </div>
        </div>
    </div>

    <script type="module" src="/js/hirings.js"></script>
<?php } ?>

<?php function drawReceivedHirings($userId) { ?>
    <div class="hirings-received">
        <h4>Received Hirings</h4>                   

        <?php
            $hirings = Hiring::getAllByUser($userId, 'owner_id');
            
            if (empty($hirings)) {
                echo "<p>No active hirings</p>";
            } else {
                echo "<div class='hirings-scrollable'>";
                    $grouped = [];
                    foreach ($hirings as $hiring) {
                        $grouped[$hiring->service_id][] = $hiring;
                    }
                    foreach ($grouped as $serviceId => $serviceHirings) {
                        $service = Service::getById((int)$serviceId);
                        $serviceTitle = htmlspecialchars($service->title);

                        $clientData = array_map(function ($h) {
                            $client = User::getById($h->client_id);
                            $clientUsername = $client ? $client->username : null;
                            return [
                                'hiring_id' => $h->id,
                                'created_at' => $h->created_at,
                                'owner_id' => $h->owner_id,
                                'client_id' => $h->client_id,
                                'client_name' => $clientUsername,
                                'status' => $h->status,
                            ];
                        }, $serviceHirings);

                        $jsonClients = htmlspecialchars(json_encode($clientData), ENT_QUOTES, 'UTF-8');
                        $escapedServiceTitle = htmlspecialchars($service->title, ENT_QUOTES, 'UTF-8');

                        echo "
                            <div class='hiring-service-group' 
                                    data-id='{$clientData[0]['hiring_id']}'
                                    onclick='highlightSelectedHiring({$clientData[0]['hiring_id']});'>
                                <div class='hiring-service-header'
                                    data-clients='$jsonClients'
                                    data-title=\"$escapedServiceTitle\"
                                    onclick='drawServiceClients(this, $serviceId, \"$serviceTitle\");'>

                                    <h5>$escapedServiceTitle</h5>
                                    <i class='fa fa-chevron-right toggle-icon'></i>
                                </div>
                            </div>
                        ";
                    }
                echo "</div>";
            }
        ?>
    </div>
<?php } ?>

<?php function drawMainHiring() { ?>
    <div class="hirings-main" id="hirings-main">
        <div class="hirings-header">
            <strong><span id="hirings-freelancer-name">[Freelancer's name]</span></strong><br>
            <small id="hirings-service-title">[Service title]</small>
        </div>

        <div class="hirings-body" id="hirings-body">
            <p><em>Select a hiring to see its details...</em></p>
        </div>
    </div>
<?php } ?>

<?php function drawOwnHirings($userId) { ?>
    <div class="hirings-own">
        <h4>Own Hirings</h4>                   

        <?php
            $hirings = Hiring::getAllByUser($userId, 'client_id');

            if (empty($hirings)) {
                echo "<p>No active hirings</p>";
            } else {
                echo "<div class='hirings-scrollable'>";
                    $grouped = [];
                    foreach ($hirings as $hiring) {
                        $grouped[$hiring->service_id][] = $hiring;
                    }

                    foreach ($grouped as $serviceId => $serviceHirings) {
                        $service = Service::getById((int)$serviceId);
                        $serviceTitle = $service->title;
                        $ownerId = (int)$service->freelancerId;
                        $userUsername = User::getById($ownerId)->username;
                        
                        $serviceHiringsJson = json_encode($serviceHirings, JSON_HEX_APOS | JSON_HEX_QUOT);

                        $userUsernameJs = json_encode($userUsername);
                        $serviceTitleJs = json_encode($serviceTitle);

                        echo "
                            <div class='hiring-service-group' 
                                    data-id='{$serviceHirings[0]->id}'
                                    onclick='highlightSelectedHiring({$serviceHirings[0]->id});'>
                                <div class='hiring-service-header'
                                    onclick='drawOwnHiringRequest($userUsernameJs, $serviceHiringsJson, $serviceId, $serviceTitleJs);'>
                                    <i class='fa fa-chevron-left'></i>
                                    <h5>$serviceTitle</h5>
                                </div>
                            </div>
                        ";
                    }
                echo "</div>";	
            }
        ?>
    </div>
<?php } ?>

<?php function drawHiringsCloseButton() { ?>
    <div class="close-button">
        <button id="hirings-close-btn"><i class="fa-solid fa-xmark"></i></button>
    </div>
<?php } ?>