import React, { useEffect, useRef } from "react";
import styles from "../styles/chess-history.module.scss";
import { cordsToTurn } from "../../../shared/game/lib";

export function ChessHistory ({gameHistory, step, setStep}) {
  const containerRef = useRef(null);

  useEffect(() => {
    containerRef.current?.lastElementChild?.scrollIntoView();
  }, [gameHistory]);

  return (
    <div className={styles.historyContainer} ref={containerRef}>
      {gameHistory?.map((turn, idx)=><div className={`${styles.turnDiv} ${step === idx + 1 ? styles.current : ""}`} onClick={()=>setStep(idx + 1)}>
        <span className={styles.idxSpan}>{idx+1}</span>
        <span className={styles.turnSpan}>{cordsToTurn(turn)}</span>
      </div>)}
    </div>
  );
}
