<?php

use TextUI\Enum\HAlign;
use TextUI\Input\Form\Crud;
use TextUI\Input\Form\Form;
use TextUI\Input\HiddenEntry;
use TextUI\Input\NumberEntry;
use TextUI\Input\ReadOnlyEntry;
use TextUI\Input\TextEntry;
use TextUI\IO;
use TextUI\Screen\Command;
require_once 'vendor/autoload.php';


// Função para gerar um UID aleatório
function generateUid() {
    return uniqid();
}

// Função para gerar um nome aleatório
function generateName() {
    $firstNames = ['John', 'Jane', 'Alex', 'Emily', 'Chris', 'Katie', 'Mike', 'Sarah'];
    $lastNames = ['Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis', 'Miller', 'Wilson'];
    return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
}

// Função para gerar uma idade aleatória
function generateAge() {
    return rand(18, 80);
}

// Função para gerar um número de telefone aleatório
function generateTelephone() {
    return '+1 ' . rand(100, 999) . '-' . rand(100, 999) . '-' . rand(1000, 9999);
}

// Função para gerar um e-mail aleatório
function generateEmail($name) {
    $domains = ['example.com', 'mail.com', 'test.org', 'random.net'];
    $nameParts = explode(' ', $name);
    $email = strtolower($nameParts[0] . '.' . $nameParts[1]) . '@' . $domains[array_rand($domains)];
    return $email;
}

// Função para gerar um estado aleatório (abreviações dos estados dos EUA)
function generateState() {
    $states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];
    return $states[array_rand($states)];
}

// Função para gerar um status aleatório
function generateStatus() {
    $statuses = ['Active', 'Inactive', 'Pending', 'Suspended'];
    return $statuses[array_rand($statuses)];
}

// Cria o array multidimensional
$tableData = [];

// Preenche o array com dados aleatórios
//for ($i = 1; $i <= 5; $i++) {
for ($i = 1; $i <= 53; $i++) {
    $name = generateName();
    $tableData[] = [
        'uid' => generateUid(),
        'name' => $name,
        'age' => generateAge(),
        'telephone' => generateTelephone(),
        'email' => generateEmail($name),
        'state' => generateState(),
        'status' => generateStatus()
    ];
}



$form = new Form('Crud Form');
$form
    ->addEntry('uid', new ReadOnlyEntry('Uid: ', generateUid()))
    ->addEntry('name', new TextEntry('Name: '))
    ->addEntry('age', new NumberEntry('Age: '))
    ->addEntry('telephone', new TextEntry('Telephone: '))
    ->addEntry('email', new TextEntry('E-mail: '))
    ->addEntry('state', new TextEntry('State: '))
    ->addEntry('status', new HiddenEntry('Pending'))
    ;

$fnInsert = function(Crud $crud) use ($form){
    $form->ask();
    $data = $crud->getData();
    $data[] = $form->getAnswers();
    $crud->setData($data);
    $crud->setMessage('🎉 Record created!');
};
$fnUpdate = function(Crud $crud) use ($form){
    $record = $crud->getSelectedRecord();
    $form->setDefaultsValues($record);
    $form->ask();
    $updated = $form->getAnswers();
    $uid = $record['uid'];
    $data = $crud->getData();
    foreach($data as $index => $row){
        if($row['uid'] === $uid){
            $data[$index] = $updated;
        }
    }
    $crud->setData($data);
    $crud->setMessage('🎉 ️Record updated!');
};
$fnDelete = function(Crud $crud){
    $index = $crud->getRecordIndex($crud->getSelectedRecord());
    echo 'Type "yes" to delete record '.$index.' or "not" to cancel: ';
    $userInput = trim(IO::readRawStdin());
    switch(strtolower($userInput)){
        case 'yes':
            $data = $crud->getData();
            unset($data[$index]);
            $crud->setData($data);
            $crud->setMessage('⚠️ ️Record deleted!');
            $crud->clearSelected();
            break;
        case 'not':
            $crud->setMessage('🤔 Deletion canceled by user!');
            break;
        default:
            $crud->setMessage('😒 User too lazy to type an option. Canceling...!');
            return;
    }
};
$fnView = function(Crud $crud){
    $record = $crud->getSelectedRecord();
    
    Command::send(Command::CLEAR_SCREEN);
    
    printf("Uid\t\t%s". PHP_EOL, $record['uid']);
    printf("Name\t\t%s". PHP_EOL, $record['name']);
    printf("Age\t\t%s". PHP_EOL, $record['age']);
    printf("Telephone\t\t%s". PHP_EOL, $record['telephone']);
    printf("E-mail\t\t%s". PHP_EOL, $record['email']);
    printf("State\t\t%s". PHP_EOL, $record['state']);
    printf("Status\t\t%s". PHP_EOL, $record['status']);
    
    echo PHP_EOL, 'Press any key to continue...';
    $trash = fgets(STDIN);
    return;
};

//$fnDelete = null;

$crud = new Crud('Create, Update, Read & Edit', $form);
$crud->setData($tableData);
$crud
    ->setTableSpecFor(id: 'name', label: 'Name', width: 0.50)
    ->setTableSpecFor(id: 'age', label: 'Age', align: HAlign::Center, width: 0.20)
    ->setTableSpecFor(id: 'status', label: 'Status', width: 0.30)
;
$crud->setInsertCallback($fnInsert);
$crud->setUpdateCallback($fnUpdate);
$crud->setDeleteCallback($fnDelete);
$crud->setViewCallback($fnView);
$crud
    ->setCharExtra('X')
    ->setLabelExtra('Extra')
    ->setExtraCallback(function(Crud $crud){$crud->setMessage('Extra function called!');})
;
//$crud->setMessage('Hello world!');

//do {
//    $crud->run();
//}while ($crud->running);

while($crud->run()){}