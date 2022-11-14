import { observer } from 'mobx-react-lite'
import { FC, useCallback, useRef } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { serverFetch } from '../../Fetch/server-fetch'
import { GridContainer } from '../../Grid/components/container'
import { gettext } from '../../Language'
import { OK } from '../../Rest/http-status'
import { template } from '../../Utils/components/template'
import { PingStore } from '../stores'
import styles from './style.module.scss'
const Items: FC = observer(() => {
  const { pingItems } = PingStore
  const items = pingItems.map(({ time }, i) => (
    <li className={styles.item} key={String(i)}>
      <span className={styles.itemNumber}>
        {i + 1 < 10 ? `0${i + 1}` : i + 1}
      </span>
      <span className={styles.itemLine}>{' ------------ '}</span>
      <span className={styles.itemTime}>{`${time} ms`}</span>
    </li>
  ))
  return <>{items}</>
})
const Results: FC = observer(() => {
  const { pingItemsCount, pingItems } = PingStore
  const timeItems = pingItems.map(({ time }) => time)
  const avg = pingItemsCount
    ? Math.floor(timeItems.reduce((a, b) => a + b, 0) / pingItemsCount)
    : 0
  const max = pingItemsCount ? Number(Math.max(...timeItems)) : 0
  const min = pingItemsCount ? Number(Math.min(...timeItems)) : 0
  return (
    <div
      className={styles.result}
      data-ping={Boolean(pingItemsCount) || undefined}
    >
      <div>
        {template(gettext('Times:{{times}}'), { times: pingItemsCount })}
      </div>
      <div>
        {template(gettext('Min:{{min}} / Max:{{max}} / Avg:{{avg}}'), {
          min,
          max,
          avg,
        })}
      </div>
    </div>
  )
})
export const Ping: FC = observer(() => {
  const { pingItemsCount } = PingStore
  const refPingTimer = useRef<number>(0)
  const refItemContainer = useRef<HTMLUListElement | null>(null)
  const ping = async (): Promise<void> => {
    const { appendPingItem } = PingStore
    const start = Number(new Date())
    const { data, status } = await serverFetch('ping')
    if (status === OK) {
      const { time } = data
      const end = Number(new Date())
      const serverTime = time * 1000
      appendPingItem({
        time: Math.floor(end - start - serverTime),
      })
      setTimeout(() => {
        if (!refItemContainer.current) {
          return
        }
        const st = refItemContainer.current.scrollTop
        const sh = refItemContainer.current.scrollHeight
        if (st < sh) {
          refItemContainer.current.scrollTop = sh
        }
      }, 100)
    }
  }
  const pingLoop = useCallback(async (): Promise<void> => {
    await ping()
    refPingTimer.current = window.setTimeout(async () => {
      await pingLoop()
    }, 1000)
  }, [])
  const onClickPing = useCallback(async () => {
    const { isPing, setIsPing } = PingStore
    if (isPing) {
      setIsPing(false)
      clearTimeout(refPingTimer.current)
      return
    }
    setIsPing(true)
    await pingLoop()
  }, [pingLoop])
  return (
    <GridContainer>
      <CardGrid
        name={
          <a className={styles.btn} onClick={onClickPing}>
            {PingStore.isPing
              ? gettext('⏸️ Stop ping')
              : gettext('👆 Start ping')}
          </a>
        }
      >
        <div className={styles.resultContainer}>
          {!pingItemsCount && <div>{gettext('No ping')}</div>}
          {Boolean(pingItemsCount) && (
            <ul className={styles.itemContainer} ref={refItemContainer}>
              <Items />
            </ul>
          )}
          {Boolean(pingItemsCount) && <Results />}
        </div>
      </CardGrid>
    </GridContainer>
  )
})
