<?php function drawHires() { ?>
    <?php 
        $session = Session::getInstance();
        $user = $session->getUser();
        $userId = $user->id;
    ?>
    <div id="hires-container">
        <button id="hires-toggle-btn">
            <i class="fa-solid fa-briefcase"></i>
            <span id="hires-badge" class="hires-badge hidden"></span>
        </button>

        <div id="hires-modal" class="hidden">
            <div class="hires-wrapper">
                <div class="hires-received">
                    <h4>Received Hirings</h4>                   

                    <?php
                        $hirings = Hire::getAllByUser($userId, 'owner_id');

                        if (empty($hirings)) {
                            echo "<p>No active hirings</p>";
                        } else {
                            $grouped = [];
                            foreach ($hirings as $hiring) {
                                $grouped[$hiring->service_id][] = $hiring;
                            }

                            foreach ($grouped as $serviceId => $serviceHirings) {
                                $service = Service::getById((int)$serviceId);
                                $serviceTitle = htmlspecialchars($service->title);

                                $clientData = array_map(function ($h) {
                                    $client = User::getById($h->client_id);
                                    return [
                                        'hiring_id' => $h->id,
                                        'client_id' => $h->client_id,
                                        'client_name' => $client->username,
                                        'status' => $h->status,
                                    ];
                                }, $serviceHirings);

                                $jsonClients = htmlspecialchars(json_encode($clientData), ENT_QUOTES, 'UTF-8');
                                $escapedServiceTitle = htmlspecialchars($service->title, ENT_QUOTES, 'UTF-8');

                                echo "
                                    <div class='hiring-service-group'>
                                        <div class='hiring-service-header'
                                            data-clients='$jsonClients'
                                            data-title=\"$escapedServiceTitle\"
                                            onclick='drawServiceClients(this)'>
                                            <h5>$escapedServiceTitle</h5>
                                            <i class='fa fa-chevron-right toggle-icon'></i>
                                        </div>
                                    </div>
                                ";
                            }
                        }
                    ?>

                </div>                    

                <div class="hires-main" id="hires-main">
                    <div class="hires-header">
                        <strong><span id="hires-freelancer-name">[Freelancer's name]</span></strong><br>
                        <small id="hires-service-title">[Service title]</small>
                    </div>

                    <div class="hires-body" id="hires-body">
                        <p><em>Select an hiring to see its details...</em></p>
                    </div>
                </div>

                <div class="hires-own">
                    <h4>Own Hirings</h4>                   

                    <?php
                        $hirings = Hire::getAllByUser($userId, 'client_id');

                        if (empty($hirings)) {
                            echo "<p>No active hirings</p>";
                        } else {
                            $grouped = [];
                            foreach ($hirings as $hiring) {
                                $grouped[$hiring->service_id][] = $hiring;
                            }

                            foreach ($grouped as $serviceId => $serviceHirings) {
                                $service = Service::getById((int)$serviceId);
                                $serviceTitle = $service->title;
                                $ownerId = (int)$service->freelancer_id;
                                $userUsername = User::getById($ownerId)->username;
                                
                                $serviceHiringsJson = json_encode($serviceHirings, JSON_HEX_APOS | JSON_HEX_QUOT);

                                $userUsernameJs = json_encode($userUsername);
                                $serviceTitleJs = json_encode($serviceTitle);

                                echo "
                                    <div class='hiring-service-group'>
                                        <div class='hiring-service-header'
                                            onclick='drawOwnHiringRequest($userUsernameJs, $ownerId, $serviceHiringsJson, $serviceTitleJs);'>
                                            <h5>$serviceTitle</h5>
                                            <i class='fa fa-chevron-right'></i>
                                        </div>
                                    </div>
                                ";
                            }
                        }
                    ?>

                </div>


                <div class="close-button">
                    <button id="hires-close-btn"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </div>
        </div>
    </div>

    <script src="/js/hires.js"></script>
<?php } ?>
