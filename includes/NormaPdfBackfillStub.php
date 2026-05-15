<?php
/**
 * Zamjena za FPDF u režimu NORMA_SETUP_BACKFILL_LOOP — ista sučelja za Cell/Ln/Row… bez crtanja,
 * da backfill bude red veličine brži (bez fontova, slika, PDF buffera).
 */
class NormaPdfBackfillStub
{
    /** @var array<int, float|int> */
    public $widths = array();

    /** @var array<int, string> */
    public $aligns = array();

    public function __call($name, $args)
    {
        return $this;
    }

    public function SetWidths($w)
    {
        $this->widths = is_array($w) ? $w : array();

        return $this;
    }

    public function SetAligns($a)
    {
        $this->aligns = is_array($a) ? $a : array();

        return $this;
    }

    /**
     * @param array<int|string, mixed> $data
     * @return $this
     */
    public function Row($data)
    {
        return $this;
    }

    /**
     * @param list<float|int> $array
     */
    public function calculateSampleStandardDeviation($array)
    {
        $count = count($array);
        if ($count <= 1) {
            return 0.0;
        }
        $mean = array_sum($array) / $count;
        $squaredDifferences = array_map(function ($value) use ($mean) {
            return pow((float) $value - $mean, 2);
        }, $array);
        $variance = array_sum($squaredDifferences) / ($count - 1);

        return sqrt($variance);
    }
}
