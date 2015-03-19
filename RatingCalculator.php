<?php

/**
 * Description of RatingCalculator
 *
 * @author Liliyan Krumov
 */
class RatingCalculator
{

    private $users = array();
    private $countriesPoints = array("Bulgaria" => 2, "Germany" => 3, "France" => 4, "Russia" => 5, "Turkey" => 6);
    private $pointsCriteria = array('calculatedRating' => 0, 'pointsCountry' => 0, 'pointsId' => 0, 'pointsYearQuarter' => 0, 'pointsAverage' => 0);
    private $groupedUsers = array("first" => array(), "second" => array(), "third" => array(), "fourth" => array());

    public function __construct() {
        $usersQuery = mysql_query("Select * From `users` order by `id` asc");

        while ($user = mysql_fetch_assoc($usersQuery)) {
            $this->users[$user['id']] = array_merge($user, $this->pointsCriteria);
        }
    }

    public function init() {
        foreach ($this->users as $id => $user) {
            $rating = 0;

            $this->rateByCountry($user, $rating);
            $this->rateById($user, $rating);
            $this->rateByYearQuarter($user, $rating);
            $this->rateByAverageRating($user, $rating);

            $this->users[$id]['calculatedRating'] = $rating;

            if (intval($rating) !== intval($user['rating'])) {
                mysql_query("Update `users` Set `rating`={$rating} Where `id`={$id}");
            }
        }
    }

    public function getUserByMail($mail) {
        foreach ($this->users as $user) {
            if ($user['email'] === $mail) {
                return $user;
            }
        }
    }

    public function getGroupedUsers() {
        $this->usersToQuarters();
        $this->sortQuarters();

        return $this->groupedUsers;
    }

    private function usersToQuarters() {
        foreach ($this->users as $id => $user) {
            $dateDetails = explode('-', $user['date']);
            $month = $dateDetails[1];

            if ($month >= 1 && $month <= 3) {
                $this->groupedUsers['first'][$id] = $user;
            } else if ($month >= 4 && $month <= 6) {
                $this->groupedUsers['second'][$id] = $user;
            } else if ($month >= 7 && $month <= 9) {
                $this->groupedUsers['third'][$id] = $user;
            } else if ($month >= 10 && $month <= 12) {
                $this->groupedUsers['fourth'][$id] = $user;
            }
        }
    }

    private function sortQuarters() {
        foreach ($this->groupedUsers as $key => $v) {
            uasort($this->groupedUsers[$key], function ($a, $b) {
                if ($a['calculatedRating'] == $b['calculatedRating']) {
                    return 0;
                }
                return ($a['calculatedRating'] > $b['calculatedRating']) ? -1 : 1;
            });
        }
    }

    private function rateByCountry(&$user, &$rating) {
        $rate = $this->countriesPoints[$user['country']];
        $this->users[$user['id']]['pointsCountry'] = $rate;
        $rating += $rate;
    }

    private function rateById(&$user, &$rating) {
        $rate = 0;

        if ($user['id'] % 3 === 0) {
            $rate = 1;
        } else if ($user['id'] % 4 === 0) {
            $rate = 2;
        }

        $this->users[$user['id']]['pointsId'] = $rate;
        $rating += $rate;
    }

    private function rateByYearQuarter(&$user, &$rating) {
        $dateDetails = explode('-', $user['date']);
        $month = $dateDetails[1];
        $multiplier = 0;

        if ($month >= 1 && $month <= 3) {
            $multiplier = 1;
        } else if ($month >= 4 && $month <= 6) {
            $multiplier = 2;
        } else if ($month >= 7 && $month <= 9) {
            $multiplier = 3;
        } else if ($month >= 10 && $month <= 12) {
            $multiplier = 4;
        }

        $this->users[$user['id']]['pointsYearQuarter'] = $multiplier;
        $rating *= $multiplier;
    }

    private function rateByAverageRating(&$user, &$rating) {
        if ($user['id'] == 1) {
            return;
        }

        $ratingSum = 0;

        for ($i = ($user['id'] - 1); $i >= 1; $i--) {
            $ratingSum += $this->users[$i]['calculatedRating'];
        }

        $ratingAverage = $ratingSum / ($user['id'] - 1);
        $decrease = 0;

        if ($rating > $ratingAverage) {
            $decrease = 5;
        }

        $this->users[$user['id']]['pointsAverage'] = $decrease;
        $rating -= $decrease;
    }

}
