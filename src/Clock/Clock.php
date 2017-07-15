<?php

namespace Krixon\MultiFactorAuth\Clock;

interface Clock
{
    public const DEFAULT_WINDOW_LENGTH = 30;


    /**
     * Returns the current time as a unix timestamp.
     *
     * @return int
     */
    public function currentTime() : int;


    /**
     * Returns the window corresponding to a specified unix timestamp.
     *
     * A window is defined as the number of time steps between an epoch (t0) the specified timestamp. For example,
     * by default t0 is equal to the unix epoch (0) and window length is equal to 30 seconds. This means unix
     * timestamp 1 is in window 0, unix timestamp 45 is in window 1 etc.
     *
     * @param int|null $time The unix timestamp for which to determine the window. If not specified, the current time
     *                       will be used.
     *
     * @return int
     */
    public function window(int $time = null) : int;


    /**
     * Returns the length of each window in seconds.
     *
     * @return int
     */
    public function windowLength() : int;


    /**
     * Returns a range of unix timestamps which correspond to windows either side of a specified unix timestamp.
     *
     * Note: this will always return the appropriate number of time, even if $time is close to the epoch. Times
     * earlier than the epoch will have a negative value. For example, assuming window length is 30 seconds, a $time
     * of 15 and an $offset of 1 will produce the windows [-15, 15, 45], because 15 corresponds to window 0.
     *
     * @param int $time   The unix timestamp for which to determine the windows.
     * @param int $offset The number of windows either side of $time which will be generated. If not specified, the
     *                    current time will be used.
     *
     * @return array
     */
    public function times(int $time = null, int $offset = 1) : array;
}
