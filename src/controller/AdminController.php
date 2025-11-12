<?php

namespace App\controller;

use App\model\Role;

class AdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showAdminSpace():void
    {
        $participates = $this->participateRepo->findAll();
        $totalCredit = 0;
        foreach($participates as $participate){
            $totalCredit += $participate->getNbSeats()*2;
        }

        $users = $this->userRepo->findUserByRole(Role::user);

        $employees = $this->userRepo->findUserByRole(Role::employe);

        $countUsers = count($users);
        $countEmployees = count($employees);


        $this->render('adminSpace', [
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'pageCss'=>'adminSpace',
            'scriptJs'=>'adminSpace',
            'totalCredit'=>$totalCredit,
            'users'=>$users,
            'countUsers'=>$countUsers,
            'employees'=> $employees,
            'countEmployees'=>$countEmployees,
        ]);
    }
}