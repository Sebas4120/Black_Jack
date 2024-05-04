<?php

class BlackjackGame {
    private $deck;
    private $userHand;
    private $dealerHand;
    private $money;
    private $bettingMoney;
    private $quit;

    public function __construct() {
        $this->deck = $this->createDeck();
        $this->userHand = [];
        $this->dealerHand = [];
        $this->money = 100; // Starting amount
        $this->bettingMoney = 0;
        $this->quit = false;
    }

    private function createDeck() {
        $suits = array("Hearts", "Diamonds", "Clubs", "Spades");
        $values = array("2", "3", "4", "5", "6", "7", "8", "9", "10", "Jack", "Queen", "King", "Ace");
        $deck = array();
        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $deck[] = array("suit" => $suit, "value" => $value);
            }
        }
        return $deck;
    }

    private function cardValue($card) {
        $value = $card['value'];
        if ($value === "Jack" || $value === "Queen" || $value === "King") {
            return 10;
        } elseif ($value === "Ace") {
            return 11; // For simplicity, Ace value is always 11
        } else {
            return intval($value);
        }
    }

    private function dealCard(&$hand) {
        $card = array_pop($this->deck);
        $hand[] = $card;
    }

    private function displayChoices() {
        echo "1. Hit (Draw another card)\n";
        echo "2. Stand (Quit drawing cards)\n";
    }

    private function welcomePlayer() {
        echo "Welcome to the Casino!\n";
        echo "Your current balance is $" . $this->money . "\n";
        echo "How much would you like to bet? ";
        $this->bettingMoney = intval(readline());
        if ($this->bettingMoney > $this->money) {
            echo "You don't have enough money.\n";
            $this->quit = true;
        }
    }

    private function calculateHandValue($hand) {
        $value = 0;
        $aces = 0;
        foreach ($hand as $card) {
            $cardValue = $this->cardValue($card);
            if ($card['value'] === "Ace") {
                $aces++;
            }
            $value += $cardValue;
        }
        // Adjust value for Aces
        while ($value > 21 && $aces > 0) {
            $value -= 10;
            $aces--;
        }
        return $value;
    }

    private function playDealer() {
        while ($this->calculateHandValue($this->dealerHand) < 17) {
            $this->dealCard($this->dealerHand);
        }
    }

    private function displayHand($hand, $hidden = false) {
        echo "Hand: ";
        foreach ($hand as $card) {
            if ($hidden) {
                echo "Hidden, ";
            } else {
                echo $card['value'] . " of " . $card['suit'] . ", ";
            }
        }
        echo "\n";
    }

    private function playGame() {
        $this->welcomePlayer();
        if ($this->quit) {
            return;
        }
        // Deal initial cards
        $this->dealCard($this->userHand);
        $this->dealCard($this->dealerHand);
        $this->dealCard($this->userHand);
        $this->dealCard($this->dealerHand);

        echo "Your Hand:\n";
        $this->displayHand($this->userHand);
        echo "Dealer's Hand:\n";
        $this->displayHand($this->dealerHand, true);

        while (!$this->quit) {
            $this->displayChoices();
            $choice = intval(readline());
            switch ($choice) {
                case 1:
                    // User draws another card
                    $this->dealCard($this->userHand);
                    echo "Your Hand:\n";
                    $this->displayHand($this->userHand);
                    if ($this->calculateHandValue($this->userHand) > 21) {
                        echo "Busted! You lose.\n";
                        $this->quit = true;
                    }
                    break;
                case 2:
                    // User stands, dealer plays
                    $this->playDealer();
                    echo "Dealer's Hand:\n";
                    $this->displayHand($this->dealerHand);
                    $dealerValue = $this->calculateHandValue($this->dealerHand);
                    $userValue = $this->calculateHandValue($this->userHand);
                    if ($dealerValue > 21 || $dealerValue < $userValue) {
                        echo "You win!\n";
                        $this->money += $this->bettingMoney;
                    } elseif ($dealerValue === $userValue) {
                        echo "It's a tie!\n";
                    } else {
                        echo "Dealer wins.\n";
                        $this->money =$this->money - $this->bettingMoney;
                    }
                    $this->quit = true;
                    break;
                default:
                    echo "Invalid choice. Please try again.\n";
            }
        }
        echo "Game over.\n";
        echo "Your current balance is $" . $this->money . "\n";
    }

    public function start() {
        $this->playGame();
    }
}

$game = new BlackjackGame();
$game->start();

?>
