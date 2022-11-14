import { FC } from 'react'
import { Grid } from '../../Grid/components/grid'
import gridStyles from '../../Grid/components/styles.module.scss'
import { formatBytes } from '../../Utils/components/format-bytes'
import styles from './styles.module.scss'
interface NetworksStatsItemProps {
  id: string
  singleLine?: boolean
  totalRx: number
  rateRx: number
  totalTx: number
  rateTx: number
}
export const NetworksStatsItem: FC<NetworksStatsItemProps> = ({
  id,
  singleLine = true,
  totalRx = 0,
  rateRx = 0,
  totalTx = 0,
  rateTx = 0,
}) => {
  if (!id) {
    return null
  }
  return (
    <div className={[styles.idRow, gridStyles.container].join(' ')}>
      <Grid lg={singleLine ? 3 : 1}>
        <div className={styles.id}>{id}</div>
      </Grid>
      <Grid lg={singleLine ? 3 : 1}>
        <div className={styles.dataContainer}>
          <div className={styles.data} data-rx>
            <div>{formatBytes(totalRx)}</div>
            <div className={styles.rateRx}>{formatBytes(rateRx)}/s</div>
          </div>
          <div className={styles.data} data-tx>
            <div>{formatBytes(totalTx)}</div>
            <div className={styles.rateTx}>{formatBytes(rateTx)}/s</div>
          </div>
        </div>
      </Grid>
    </div>
  )
}
