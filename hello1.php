<?php

class IterationCalculator
{
    protected $pradzia;
    protected $galas;
    protected $iter_counts = [];

    public function __construct($pradzia, $galas)
    {
        $this->pradzia = $pradzia;
        $this->galas = $galas;
        $this->calculateIterations();
    }

    protected function calculateIterations()
    {
        foreach (range($this->pradzia, $this->galas) as $s) {
            $seka = [$s]; 
            $max = 1;
            $min = $s; 
            $iteracijos = 1;

            while ($s > 1) {
                if ($s % 2 == 0) {
                    $s = $s / 2;
                } else {
                    $s = 3 * $s + 1;
                }

                array_push($seka, $s); 
                $iteracijos++;
                $max = max($max, $s); 
                $min = min($min, $s); 
            }

            $this->iter_counts[] = $iteracijos;
        }
    }

    public function getIterationCounts()
    {
        return $this->iter_counts;
    }
}

class HistogramGenerator extends IterationCalculator
{
    protected $width;
    protected $height;

    public function __construct($pradzia, $galas, $width = 600, $height = 400)
    {
        parent::__construct($pradzia, $galas);
        $this->width = $width;
        $this->height = $height;
    }

    public function generateHistogram()
    {
        $iter_counts = $this->getIterationCounts();
        $image = imagecreate($this->width, $this->height);

        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $bar_color = imagecolorallocate($image, 0, 0, 255);

        imagefill($image, 0, 0, $white);

        $bar_width = $this->width / count($iter_counts);
        $max_count = max($iter_counts);

        foreach ($iter_counts as $key => $count) {
            $x1 = $key * $bar_width;
            $y1 = $this->height - ($count / $max_count) * ($this->height - 50);
            $x2 = ($key + 1) * $bar_width - 5;
            $y2 = $this->height;
            imagefilledrectangle($image, $x1, $y1, $x2, $y2, $bar_color);
            imagestring($image, 3, $x1 + 2, $y1 - 15, $count, $black);
        }

        $image_file = 'histogram.png';
        imagepng($image, $image_file);
        imagedestroy($image);

        return $image_file;
    }
}


if (isset($_GET['st']) && isset($_GET['pb'])) {
    $pradzia = intval($_GET['st']);
    $galas = intval($_GET['pb']);
} else {
    $pradzia = 1;
    $galas = 10;
}

$histogramGenerator = new HistogramGenerator($pradzia, $galas);
$image_file = $histogramGenerator->generateHistogram();
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <img src="<?php echo $image_file; ?>" alt="Histogram">
    <br><br>
</body>
</html>
