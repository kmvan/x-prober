import { GUTTER } from '@/Config/src'
import { serverFetch } from '@/Fetch/src/server-fetch'
import { Grid } from '@/Grid/src/components/grid'
import { Row } from '@/Grid/src/components/row'
import { gettext } from '@/Language/src'
import { ProgressBar } from '@/ProgressBar/src/components'
import { OK } from '@/Restful/src/http-status'
import { SysLoadGroup } from '@/ServerStatus/src/components/system-load'
import {
  ServerStatusCpuUsageProps,
  ServerStatusUsageProps,
} from '@/ServerStatus/src/stores'
import { Alert } from '@/Utils/src/components/alert'
import { Loading } from '@/Utils/src/components/loading'
import { template } from '@/Utils/src/components/template'
import { observer } from 'mobx-react-lite'
import React, { useCallback, useEffect } from 'react'
import styled from 'styled-components'
import { NodesStore } from '../stores'
import { NodeNetworks } from './node-networks'
const StyledNodeGroupId = styled.a`
  display: block;
  text-decoration: underline;
  text-align: center;
  margin-bottom: calc(${GUTTER} / 2);
  :hover {
    text-decoration: none;
  }
`
const StyledNodeGroup = styled.div`
  margin-bottom: calc(${GUTTER} / 2);
`
const StyledNodeGroupMsg = styled(StyledNodeGroup)`
  display: flex;
  justify-content: center;
`
const SysLoad = ({ sysLoad }: { sysLoad: number[] }) => {
  if (!sysLoad?.length) {
    return null
  }
  return (
    <StyledNodeGroup>
      <SysLoadGroup isCenter sysLoad={sysLoad} />
    </StyledNodeGroup>
  )
}
const Cpu = ({ cpuUsage }: { cpuUsage: ServerStatusCpuUsageProps }) => {
  return (
    <StyledNodeGroup>
      <ProgressBar
        title={template(
          gettext(
            'idle: {{idle}} \nnice: {{nice}} \nsys: {{sys}} \nuser: {{user}}'
          ),
          cpuUsage
        )}
        value={100 - cpuUsage.idle}
        max={100}
        isCapacity={false}
        left={gettext('CPU usage')}
      />
    </StyledNodeGroup>
  )
}
const Memory = ({ memRealUsage }: { memRealUsage: ServerStatusUsageProps }) => {
  const { value = 0, max = 0 } = memRealUsage
  if (!max) {
    return null
  }
  const percent = Math.floor((value / max) * 10000) / 100
  return (
    <StyledNodeGroup>
      <ProgressBar
        title={template(gettext('Usage: {{percent}}'), {
          percent: `${percent.toFixed(1)}%`,
        })}
        value={value}
        max={max}
        isCapacity
        left={gettext('Memory')}
      />
    </StyledNodeGroup>
  )
}
const Swap = ({ swapUsage }: { swapUsage: ServerStatusUsageProps }) => {
  const { value = 0, max = 0 } = swapUsage
  if (!max) {
    return null
  }
  const percent = Math.floor((value / max) * 10000) / 100
  return (
    <StyledNodeGroup>
      <ProgressBar
        title={template(gettext('Usage: {{percent}}'), {
          percent: `${percent.toFixed(1)}%`,
        })}
        value={value}
        max={max}
        isCapacity
        left={gettext('Swap')}
      />
    </StyledNodeGroup>
  )
}
const Items = observer(() => {
  const items = NodesStore.items.map(
    ({ id, url, isLoading, isError, errMsg, data }) => {
      const idLink = <StyledNodeGroupId href={url}>{id}</StyledNodeGroupId>
      switch (true) {
        case isLoading:
          return (
            <Grid key={id} tablet={[1, 4]} mobileLg={[1, 2]}>
              {idLink}
              <StyledNodeGroupMsg>
                <Loading>{gettext('Fetching...')}</Loading>
              </StyledNodeGroupMsg>
            </Grid>
          )
        case isError:
          return (
            <Grid key={id} tablet={[1, 4]} mobileLg={[1, 2]}>
              {idLink}
              <StyledNodeGroupMsg>
                <Alert isSuccess={false} msg={errMsg} />
              </StyledNodeGroupMsg>
            </Grid>
          )
      }
      const { serverStatus, networkStats } = data
      return (
        <Grid
          key={id}
          tablet={[1, 2]}
          desktopSm={[1, 3]}
          desktopMd={[1, 4]}
          desktopLg={[1, 6]}>
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
    }
  )
  return <>{items}</>
})
export const Nodes = observer(() => {
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
  }, [])
  return (
    <Row>
      <Items />
    </Row>
  )
})
