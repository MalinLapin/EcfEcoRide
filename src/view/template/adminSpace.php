<section class="adminSpace robotoRegular">
        
        <!-- Titre -->
        <div class="adminTitle"> 
            <h2 class="robotoBold">Tableau de bord administrateur</h2>
            <div class="adminInfo">
                <span class="material-symbols-outlined">admin_panel_settings</span>
            </div>
        </div>

        <!-- Stats principal -->
        <div class="statsDiv">
            <div class="statCard totalCredits">
                <div class="statIcon">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <div class="statInfo">
                    <p class="statValue robotoBold" id="totalCredits"><?= htmlspecialchars($totalCredit) ?></p>
                    <p class="statLabel">Crédits totaux gagnés</p>
                </div>
            </div>

            <div class="statCard totalRides">
                <div class="statIcon">
                    <span class="material-symbols-outlined">road</span>
                </div>
                <div class="statInfo">
                    <p class="statValue robotoBold" id="totalRides"><?= htmlspecialchars($countUsers); ?></p>
                    <p class="statLabel">Covoiturages réalisés</p>
                </div>
            </div>

            <div class="statCard totalUsers">
                <div class="statIcon">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <div class="statInfo">
                    <p class="statValue robotoBold" id="totalUsers"><?= htmlspecialchars($countUsers); ?></p>
                    <p class="statLabel">Utilisateurs inscrits</p>
                </div>
            </div>

            <div class="statCard totalEmployees">
                <div class="statIcon">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <div class="statInfo">
                    <p class="statValue robotoBold" id="totalEmployees"><?= htmlspecialchars($countEmployees); ?></p>
                    <p class="statLabel">Employés actifs</p>
                </div>
            </div>
        </div>

        <!-- Graphique -->
        <div class="chartsDiv">
            <div class="chartCard">
                <div class="chartHeader">
                    <h3 class="robotoBold">
                        <span class="material-symbols-outlined">show_chart</span>
                        Covoiturages par jour
                    </h3>
                    
                </div>
                <div class="chartContainer">
                    <canvas id="ridesChart"></canvas>
                </div>
            </div>

            <div class="chartCard">
                <div class="chartHeader">
                    <h3 class="robotoBold">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                        Crédits gagnés par jour
                    </h3>
                </div>
                <div class="chartContainer">
                    <canvas id="creditsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gestion des employés -->
        <div class="employeeDiv">
            <div class="employeeHeader">
                <h3 class="robotoBold">
                    <span class="material-symbols-outlined">manage_accounts</span>
                    Gestion des employés
                </h3>
                <button class="btnAddEmployee robotoBold" id="btnAddEmployee">
                    <span class="material-symbols-outlined">person_add</span>
                    Ajouter un employé
                </button>
            </div>

            <div class="tableContainer">
                <table class="dataTable">
                    <thead>
                        <tr>
                            <th class="robotoBold">ID</th>
                            <th class="robotoBold">Nom</th>
                            <th class="robotoBold">Email</th>
                            <th class="robotoBold">Date de création</th>
                            <th class="robotoBold">Statut</th>
                            <th class="robotoBold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="employeesTableBody">
                        <?php foreach ($employees as $employee): ?>
                        <tr data-user-id="<?= $employee->getIdUser(); ?>">
                            <td>#<?= $employee->getIdUser(); ?></td>
                            <td><?= htmlspecialchars($employee->getPseudo()); ?></td>
                            <td><?= htmlspecialchars($employee->getEmail()); ?></td>
                            <td><?= $employee->getCreatedAt()->format('d/m/Y'); ?></td>
                            <td>
                                <span class="statusBadge <?= $employee->getIsActive() ? 'active' : 'suspended'; ?>">
                                    <?= $employee->getIsActive() ? 'Actif' : 'Suspendu'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btnSuspend <?= $employee->getIsActive() ? 'active' : 'suspended'; ?>"
                                        title="<?= $employee->getIsActive() ? 'Suspendre' : 'Réactiver'; ?>">
                                    <span class="material-symbols-outlined">
                                        <?= $employee->getIsActive() ? 'block' : 'check_circle'; ?>
                                    </span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Gestion des users -->
        <div class="userDiv">
            <div class="userHeader">
                <h3 class="robotoBold">
                    <span class="material-symbols-outlined">group</span>
                    Gestion des utilisateurs
                </h3>
            </div>

            <div class="tableContainer">
                <table class="dataTable">
                    <thead>
                        <tr>
                            <th class="robotoBold">ID</th>
                            <th class="robotoBold">Pseudo</th>
                            <th class="robotoBold">Email</th>
                            <th class="robotoBold">Date d'inscription</th>
                            <th class="robotoBold">Statut</th>
                            <th class="robotoBold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        <?php foreach ($users as $user): ?>
                        <tr data-user-id="<?= $user->getIdUser(); ?>">
                            <td>#<?= $user->getIdUser(); ?></td>
                            <td><?= htmlspecialchars($user->getPseudo()); ?></td>
                            <td><?= htmlspecialchars($user->getEmail()); ?></td>
                            <td><?= $user->getCreatedAt()->format('d/m/Y'); ?></td>
                            <td>
                                <span class="statusBadge <?= $user->getIsActive() ? 'active' : 'suspended'; ?>">
                                    <?= $user->getIsActive() ? 'Actif' : 'Suspendu'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btnSuspend <?= $user->getIsActive() ? 'active' : 'suspended'; ?>"
                                        title="<?= $user->getIsActive() ? 'Suspendre' : 'Réactiver'; ?>">
                                    <span class="material-symbols-outlined">
                                        <?= $user->getIsActive() ? 'block' : 'check_circle'; ?>
                                    </span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <!-- MODAL ajout employé (caché par défaut)-->
    <div id="adminModal" class="adminModal">
        <div class="modalContent">
            <div class="modalHeader">
                <h3 class="robotoBold">Créer un compte employé</h3>
                <button class="closeModal modalCancel">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="modalForm" class="modalForm">
                <div class="formGroup">
                    <label>
                        <span class="material-symbols-outlined">person</span>
                        Nom complet
                    </label>
                    <input type="text" id="employeeName" placeholder="Jean Dupont" required>
                </div>

                <div class="formGroup">
                    <label>
                        <span class="material-symbols-outlined">email</span>
                        Email
                    </label>
                    <input type="email" id="employeeEmail" placeholder="jean.dupont@entreprise.com" required>
                </div>

                <div class="formGroup">
                    <label>
                        <span class="material-symbols-outlined">lock</span>
                        Mot de passe
                    </label>
                    <input type="password" id="employeePassword" placeholder="Minimum 12 caractères avec chiffre et caractères spéciaux" required>
                </div>

                <div class="modalFooter">
                    <button type="button" class="btnCancel modalCancel">
                        Annuler
                    </button>
                    <button type="submit" class="btnSubmit">
                        <span class="material-symbols-outlined">add</span>
                        Créer le compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>