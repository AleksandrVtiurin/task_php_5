<?php
function calculateWorkSchedule(int $year, int $month): array
{
    $daysInMonth = date('t', strtotime("$year-$month-01"));
    $workDays = [];
    $currentDay = 1;
    $daysOffCount = 0;
    $isWorkDay = true;
    $workDays[1] = true;
    while ($currentDay <= $daysInMonth) {
        if ($isWorkDay) {
            $dayOfWeek = date('N', strtotime("$year-$month-$currentDay"));
            if ($dayOfWeek >= 6) { 
                $nextMonday = $currentDay;
                while (date('N', strtotime("$year-$month-$nextMonday")) != 1) {
                    $nextMonday++;
                    if ($nextMonday > $daysInMonth) {
                        break;
                    }
                }
                
                if ($nextMonday <= $daysInMonth) {
                    $workDays[$nextMonday] = true;
                    $currentDay = $nextMonday;
                }
            } else {
                $workDays[$currentDay] = true;
            }
            $daysOffCount = 2;
            $isWorkDay = false;
        } else {
            $daysOffCount--;
            if ($daysOffCount == 0) {
                $isWorkDay = true;
            }
        }
        
        $currentDay++;
    }
    
    return $workDays;
}

function displayCalendar(int $year, int $month): void
{
    $monthNames = [
        1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
        5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
        9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
    ];
    
    $monthName = $monthNames[$month];
    $daysInMonth = date('t', strtotime("$year-$month-01"));
    $workDays = calculateWorkSchedule($year, $month);
    echo "\n" . str_repeat('=', 40) . "\n";
    echo "\033[1;36m" . sprintf("%s %d", $monthName, $year) . "\033[0m\n";
    echo str_repeat('=', 40) . "\n";
    $weekDays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
    echo implode('  ', $weekDays) . "\n";
    echo str_repeat('-', 35) . "\n";
    $firstDayOfWeek = date('N', strtotime("$year-$month-01"));
    echo str_repeat('    ', $firstDayOfWeek - 1);
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dayOfWeek = date('N', strtotime("$year-$month-$day"));
        $dayFormatted = sprintf("%2d", $day);
        
        if (isset($workDays[$day])) {
            echo "\033[1;32m" . $dayFormatted . "+\033[0m";
        } elseif ($dayOfWeek >= 6) {
            echo "\033[1;31m" . $dayFormatted . " \033[0m";
        } else {
            echo $dayFormatted . " ";
        }
        
        if ($dayOfWeek == 7) {
            echo "\n";
        } else {
            echo " ";
        }
    }
    
    echo "\n" . str_repeat('=', 40) . "\n";
    $totalWorkDays = count($workDays);
    echo "Всего рабочих дней: \033[1;32m$totalWorkDays\033[0m\n";
    echo "Рабочие дни: ";
    $workDayList = [];
    foreach (array_keys($workDays) as $workDay) {
        $workDayList[] = $workDay;
    }
    echo implode(', ', $workDayList) . "\n";
}

function parseCommandLineArguments(): array
{
    $year = date('Y');
    $month = date('m');
    $monthsCount = 1;
    
    global $argv;
    
    if (isset($argv[1]) && isset($argv[2])) {
        $year = (int)$argv[1];
        $month = (int)$argv[2];
        
        if ($month < 1 || $month > 12) {
            echo "Ошибка: Месяц должен быть от 1 до 12\n";
            exit(1);
        }
        
        if ($year < 1900 || $year > 2100) {
            echo "Ошибка: Год должен быть между 1900 и 2100\n";
            exit(1);
        }
        
        if (isset($argv[3])) {
            $monthsCount = (int)$argv[3];
            if ($monthsCount < 1) {
                $monthsCount = 1;
            }
        }
    }
    
    return [$year, $month, $monthsCount];
}

function main(): void
{
    list($startYear, $startMonth, $monthsCount) = parseCommandLineArguments();
    
    for ($i = 0; $i < $monthsCount; $i++) {
        $currentYear = $startYear;
        $currentMonth = $startMonth + $i;    
        while ($currentMonth > 12) {
            $currentMonth -= 12;
            $currentYear += 1;
        }
        
        displayCalendar($currentYear, $currentMonth);
    
        if ($i < $monthsCount - 1) {
            echo "\n";
        }
    }
}

main();

?>
