import { observer } from 'mobx-react-lite'
import { FC, useCallback, useEffect } from 'react'
import { serverFetch } from '../../Fetch/server-fetch'
import { GridContainer } from '../../Grid/components/container'
import { Grid } from '../../Grid/components/grid'
import { gettext } from '../../Language'
import { ProgressBar } from '../../ProgressBar/components'
import { OK } from '../../Rest/http-status'
import { SysLoadGroup } from '../../ServerStatus/components/system-load'
import {
  ServerStatusCpuUsageProps,
  ServerStatusUsageProps,
} from '../../ServerStatus/typings'
import { Alert } from '../../Utils/components/alert'
import { Loading } from '../../Utils/components/loading'
import { template } from '../../Utils/components/template'
import { NodesStore } from '../stores'
import { NodeNetworks } from './node-networks'
import styles from './styles.module.scss'
const SysLoad: FC<{ sysLoad: number[] }> = ({ sysLoad }) => {
  if (!sysLoad?.length) {
    return null
  }
  return (
    <div className={styles.group}>
      <SysLoadGroup isCenter sysLoad={sysLoad} />
    </div>
  )
}
const Cpu: FC<{ cpuUsage: ServerStatusCpuUsageProps }> = ({ cpuUsage }) => (
  <div className={styles.group}>
    <ProgressBar
      title={template(
        gettext(
          'idle: {{idle}} \nnice: {{nice}} \nsys: {{sys}} \nuser: {{user}}',
        ),
        cpuUsage as any,
      )}
      value={100 - cpuUsage.idle}
      max={100}
      isCapacity={false}
      left={gettext('CPU usage')}
    />
  </div>
)
const Memory: FC<{ memRealUsage: ServerStatusUsageProps }> = ({
  memRealUsage,
}) => {
  const { value = 0, max = 0 } = memRealUsage
  if (!max) {
    return null
  }
  const percent = Math.floor((value / max) * 10000) / 100
  return (
    <div className={styles.group}>
      <ProgressBar
        title={template(gettext('Usage: {{percent}}'), {
          percent: `${percent.toFixed(1)}%`,
        })}
        value={value}
        max={max}
        isCapacity
        left={gettext('Memory')}
      />
    </div>
  )
}
const Swap: FC<{ swapUsage: ServerStatusUsageProps }> = ({ swapUsage }) => {
  const { value = 0, max = 0 } = swapUsage
  if (!max) {
    return null
  }
  const percent = Math.floor((value / max) * 10000) / 100
  return (
    <div className={styles.group}>
      <ProgressBar
        title={template(gettext('Usage: {{percent}}'), {
          percent: `${percent.toFixed(1)}%`,
        })}
        value={value}
        max={max}
        isCapacity
        left={gettext('Swap')}
      />
    </div>
  )
}
const Items: FC = observer(() => {
  const items = NodesStore.items.map(
    ({ id, url, isLoading, isError, errMsg, data }) => {
      const idLink = (
        <a className={styles.groupId} href={url}>
          {id}
        </a>
      )
      switch (true) {
        case isLoading:
          return (
            <Grid key={id} lg={2} xl={3}>
              {idLink}
              <div className={styles.groupMsg}>
                <Loading>{gettext('Fetching...')}</Loading>
              </div>
            </Grid>
          )
        case isError:
          return (
            <Grid key={id} lg={2} xl={3}>
              {idLink}
              <div className={styles.groupMsg}>
                <Alert isSuccess={false} msg={errMsg} />
              </div>
            </Grid>
          )
        default:
      }
      const { serverStatus, networkStats } = data
      return (
        <Grid key={id} lg={2} xl={3}>
          {idLink}
          <SysLoad sysLoad={serverStatus.sysLoad} />
          <Cpu cpuUsage={serverStatus?.cpuUsage} />
          <Memory memRealUsage={serverStatus?.memRealUsage} />
          <Swap swapUsage={serverStatus?.swapUsage} />
          <NodeNetworks
            items={networkStats?.networks || []}
            timestamp={networkStats?.timestamp || 0}
          />
        </Grid>
      )
    },
  )
  return <>{items}</>
})
export const Nodes: FC = observer(() => {
  const { items, itemsCount } = NodesStore
  const fetch = useCallback(async (nodeId: string) => {
    const { setItem } = NodesStore
    const { data: item, status } = await serverFetch(`node&nodeId=${nodeId}`)
    if (status === OK) {
      if (!item) {
        return
      }
      setItem({ id: nodeId, isLoading: false, data: item })
      // fetch again
      setTimeout(() => {
        fetch(nodeId)
      }, 1000)
    } else {
      setItem({
        id: nodeId,
        isLoading: false,
        isError: true,
        errMsg: template(gettext('Fetch failed. Node returns {{code}}.'), {
          code: status,
        }),
      })
    }
  }, [])
  useEffect(() => {
    if (itemsCount) {
      for (const { id } of items) {
        fetch(id)
      }
    }
  }, [fetch, items, itemsCount])
  return (
    <GridContainer>
      <Items />
    </GridContainer>
  )
})
