# 权限判定按四层拆分，不引入 Policy

Api 面的授权拆成四层，各归各位：**认证**由 `auth` 中间件负责；**角色门禁**由 `CheckRole` 中间件负责，Admin 与 Api 共用（两者是同一套 session 登录态）；**数据归属**不进中间件，收敛为 User 上的 `ownedEnterprise()` / `approvedEnterprise()` 两个入口；**账号状态门禁**（企业是否过审）并入 `approvedEnterprise()`。这四层的定义见 `CONTEXT.md`。

数据归属之所以不能交给中间件：中间件运行在路由匹配之后、控制器之前，此刻它只知道"谁 + 哪个路由"，而 `{id}` 指向的记录归谁必须查库才知道。让中间件承担这件事，等于让它在每个资源上重做一遍控制器的前半截。

不引入 Laravel Policy：Policy 解决的是"多资源 × 多动作"的授权矩阵，而本项目只有单一租户维度（Enterprise 拥有自己的 Job / Application / Interview / Offer）、二十处同一模式。引入它需要注册、改写全部调用点并补测试，换来的表达力当前用不上。待出现主体类型内部的再分级——例如企业 HR 与企业管理员权限不同——时再评估。

## Consequences

`CheckRole` 需要在 Api 面复用，其失败响应必须区分 HTML 重定向（Admin）与 JSON 响应（Api）；当前的 `redirect()->route('admin.login')` 直接复用到 Api 会返回 302 HTML，前端拿到的是登录页而不是错误码。
